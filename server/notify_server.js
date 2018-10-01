const WSServer = require('./modules/WSServer');
const wsserver = new WSServer(8080);
setTimeout(() => {
  wsserver.send(1, 'wddw');
}, 5000);
