
(function(){
    angular.module('app.http-services', [])
        .factory('DummyService', ['$q', function($q){
            return {
                getDummyData: getDummyData
            };

            function getDummyData(){
                var deferred = $q.defer();

                var data = [{
                    "id": 860,
                    "firstName": "Superman",
                    "lastName": "Yoda"
                }, {
                    "id": 870,
                    "firstName": "Foo",
                    "lastName": "Whateveryournameis"
                }, {
                    "id": 590,
                    "firstName": "Toto",
                    "lastName": "Titi"
                }, {
                    "id": 803,
                    "firstName": "Luke",
                    "lastName": "Kyle"
                }];


                setTimeout(function(){
                   deferred.resolve(data);
                }, 500);

                return deferred.promise;
            }
        }]);
})();
