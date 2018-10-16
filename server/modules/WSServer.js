'use strict';

const WebSocket = require('ws');
const axios = require('axios');

class WSServer {
  constructor (port, getUserUrl) {
    this.getUserUrl = getUserUrl;

    this.server = new WebSocket.Server({
      port: port
    });

    this.server.on('connection', (socket, request) => {
      this.connectionHandler(socket, request);
    });
  }

  getToken (cookies) {
    let token = '';
    cookies.split(';').forEach(function (cookie) {
      const parts = cookie.split('=');
      if (parts.shift() === 'token') {
        token = parts.shift();
        return false;
      }
    });

    return token;
  }

  connectionHandler (socket, request) {
    if (!request.headers.cookie) {
      socket.close();
      return;
    }

    const token = this.getToken(request.headers.cookie);
    if (!token) {
      socket.close();
      return;
    }

    socket.authUserId = null;

    axios.get(this.getUserUrl, {
      headers: {
        'Authorization': 'Bearer ' + token
      }
    })
      .then(response => {
        if (response.data.id) {
          socket.authUserId = response.data.id;
          return;
        }

        socket.close();
      })
      .catch(() => {
        socket.close();
        return;
      });

    socket.on('message', data => {
      if (socket.authUserId && this.reciever) {
        this.reciever(data, socket);
      }

    });
  }

  send (userId, message) {
    if (!userId) {
      return;
    }

    this.server.clients.forEach((client, index) => {
      if (client.authUserId === userId) {
        client.send(message);
      }
    });
  }

  setReciever (callback) {
    this.reciever = callback;
  }
}

module.exports = WSServer;
