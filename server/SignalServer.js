require('dotenv').config({ path: '../.env' });

if (process.env.SIGNAL_SERVER_ENABLED !== 'true') {
  return;
}

const WSServer = require('./modules/WSServer');
const wsServer = new WSServer(process.env.SIGNAL_SERVER_WS_PORT, process.env.APP_URL + '/user');

wsServer.setReciever((data, socket) => {
  wsServer.send(socket.authUserId, 'Recieved: ' + data);
});

const UDPServer = require('./modules/UDPServer');
const udpServer = new UDPServer(process.env.SIGNAL_SERVER_UDP_PORT, '127.0.0.1');

udpServer.setReciever((data, socket) => {
  data = data + '';
  if (!data.match(/^UPDATED_USER_ID:\d+$/)) {
    return;
  }

  const parts = data.split(':');
  const userId = +parts.pop();
  if (!userId) {
    return;
  }

  wsServer.send(userId, 'UPDATED');
});
