angular.module('app.socket-services', ['app.site-configs', 'app.shared-helpers', 'btford.socket-io'])
    .factory('DexTraderSocket', ['$site-configs', 'socketFactory', function ($configs, socketFactory) {

        var myIoSocket = io.connect($configs.SOCKET_HOST);

        var mySocket = socketFactory({
            ioSocket: myIoSocket
        });

        return mySocket;
    }]);
