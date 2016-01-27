var app = require('express')(),
	server = require('http').Server(app),
	io = require('socket.io')(server),
	Redis = require('ioredis'),
	redis = new Redis(),
	port = process.env.PORT || 3000;

redis.subscribe('signal.add', function(err, count) {
	console.log("subscribe");
});

//app.get('/', function(req, res) {
//	res.send('hello world');
//});

io.on('connection', function (socket) {
	console.log("new client connected");
	redis.on('message', function(channel, message) {
		socket.emit(channel, message);
	});

	socket.on('disconnect', function() {
		console.log("new client disconnect");
		redis.quit();
	});

});

server.listen(port, function(){
	console.log('listening on ' + port);
});