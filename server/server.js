var io = require('socket.io').listen(4568);
io.sockets.on('connection', function(socket){
	socket.on('new_message', function(data){
		io.sockets.emit('new_message', data);
	});
});