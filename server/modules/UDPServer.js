'use strict';

const dgram = require('dgram');

class UDPServer {
  constructor (port, addr) {
    this.server = dgram.createSocket('udp4');

    this.server.on('message', (data, socket) => {
      if (this.reciever) {
        this.reciever(data, socket);
      }
    });

    this.server.bind(port, addr);
  }

  setReciever (callback) {
    this.reciever = callback;
  }
}

module.exports = UDPServer;
