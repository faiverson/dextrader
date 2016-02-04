var app = require('express')(),
	server = require('http').Server(app),
	io = require('socket.io')(server),
	Redis = require('ioredis'),
	redis = new Redis(),
	port = process.env.PORT || 3000;

server.listen(port, function(){
	console.log('listening on ' + port);
});

redis.psubscribe('signal.*', function(err, count) {
	console.log("subscribed to signal channel");
});

redis.on('pmessage', function(pattern, channel, message) {
	console.log(channel);
	io.emit(channel, message);
});

io.on('connection', function (socket) {
	console.log("Client connected");
});

io.on('disconnect', function() {
	console.log("Client disconnect");
});