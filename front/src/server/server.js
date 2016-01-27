var app = require('express')();
var server = require('http').Server(app);
var io = require('socket.io')(server);
//var redis = require('redis');
var Redis = require('ioredis');
var redis = new Redis();

server.listen(6379, function(){
	console.log('listening on *:3001');
});

function handler(req, res) {
	res.writeHead(200);
	res.end('');
}

redis.psubscribe('*', function(err, count) {
	console.log("psubscribe");
});

redis.on('pmessage', function(subscribed, channel, message) {
	console.log("pmessage");
});

io.on('connection', function (socket) {

	console.log("new client connected");
	//var redisClient = redis.createClient();
	//redisClient.subscribe('message');

	redisClient.on("message", function(channel, message) {
		console.log("mew message in queue "+ message + "channel");
		socket.emit(channel, message);
	});

	socket.on('disconnect', function() {
		redisClient.quit();
	});

});