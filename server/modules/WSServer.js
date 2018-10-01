'use strict';

const apiURL = 'http://laranuxt';
const WebSocket = require('ws');
const axios = require('axios');

class WSServer {
  constructor (port) {
    this.server = new WebSocket.Server({
      port: port
    });

    this.server.on('connection', this.connectionHandler);
  }

  static getToken (cookies) {
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

    const token = WSServer.getToken(request.headers.cookie);
    if (!token) {
      socket.close();
      return;
    }

    socket.authUserId = null;

    axios.get(apiURL + '/user', {
      headers: {
        'Authorization': 'Bearer ' + token
      }
    })
      .then(function (response) {
        if (response.data.id) {
          socket.authUserId = response.data.id;
          return;
        }

        socket.close();
      })
      .catch(function (error) {
        socket.close();
      });

    socket.on('message', data => {
      console.log(data);
      // console.log(socket);
      // Authorization: Bearer
    });
  }

  send (userId, message) {
    this.server.clients.forEach((client, index) => {
      if (client.authUserId === userId) {
        client.send(message);
      }
    });
  }
}

module.exports = WSServer;
