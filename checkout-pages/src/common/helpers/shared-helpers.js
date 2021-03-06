angular.module('app.shared-helpers', [])
    .factory('$objects', function () {
        return {
            'isEmpty': function (obj) {
                return Object.keys(obj).length === 0;
            },
            'cleanArray': function cleanArray(actual) {
                var newArray = [];
                for (var i = 0; i < actual.length; i++) {
                    if (actual[i]) {
                        newArray.push(actual[i]);
                    }
                }
                return newArray;
            },
            'toUrlString': function (obj) {
                var url = this.cleanArray(Object.keys(obj).map(function (k) {
                    if (!angular.isUndefined(obj[k]) && obj[k] !== "") {
                        return encodeURIComponent(k) + '=' + encodeURIComponent(obj[k]);
                    }
                })).join('&');
                return url;
            }
        };
    })
    .factory('os-info', function () {
        function getOS() {
            var os = 'Unknown',
                screenSize = '',
                device = '',
                nVer = navigator.appVersion,
                nAgt = navigator.userAgent,
                browser = navigator.appName,
                version = '' + parseFloat(navigator.appVersion),
                majorVersion = parseInt(navigator.appVersion, 10),
                mobile = /Mobile|mini|Fennec|Android|iP(ad|od|hone)/.test(nVer),
                clientStrings = [
                    {s: 'Windows 3.11', r: /Win16/},
                    {s: 'Windows 95', r: /(Windows 95|Win95|Windows_95)/},
                    {s: 'Windows ME', r: /(Win 9x 4.90|Windows ME)/},
                    {s: 'Windows 98', r: /(Windows 98|Win98)/},
                    {s: 'Windows CE', r: /Windows CE/},
                    {s: 'Windows 2000', r: /(Windows NT 5.0|Windows 2000)/},
                    {s: 'Windows XP', r: /(Windows NT 5.1|Windows XP)/},
                    {s: 'Windows Server 2003', r: /Windows NT 5.2/},
                    {s: 'Windows Vista', r: /Windows NT 6.0/},
                    {s: 'Windows 7', r: /(Windows 7|Windows NT 6.1)/},
                    {s: 'Windows 8.1', r: /(Windows 8.1|Windows NT 6.3)/},
                    {s: 'Windows 8', r: /(Windows 8|Windows NT 6.2)/},
                    {s: 'Windows NT 4.0', r: /(Windows NT 4.0|WinNT4.0|WinNT|Windows NT)/},
                    {s: 'Windows ME', r: /Windows ME/},
                    {s: 'Android', r: /Android/},
                    {s: 'Open BSD', r: /OpenBSD/},
                    {s: 'Sun OS', r: /SunOS/},
                    {s: 'Linux', r: /(Linux|X11)/},
                    {s: 'iOS', r: /(iPhone|iPad|iPod)/},
                    {s: 'Mac OS X', r: /Mac OS X/},
                    {s: 'Mac OS', r: /(MacPPC|MacIntel|Mac_PowerPC|Macintosh)/},
                    {s: 'QNX', r: /QNX/},
                    {s: 'UNIX', r: /UNIX/},
                    {s: 'BeOS', r: /BeOS/},
                    {s: 'OS/2', r: /OS\/2/},
                    {s: 'Search Bot', r: /(nuhk|Googlebot|Yammybot|Openbot|Slurp|MSNBot|Ask Jeeves\/Teoma|ia_archiver)/}
                ],
                nameOffset, width, height, verOffset, osVersion, ix;

            if (screen.width) {
                width = (screen.width) ? screen.width : '';
                height = (screen.height) ? screen.height : '';
                screenSize += '' + width + " x " + height;
            }

            // Opera
            if ((verOffset = nAgt.indexOf('Opera')) !== -1) {
                browser = 'Opera';
                version = nAgt.substring(verOffset + 6);
                if ((verOffset = nAgt.indexOf('Version')) !== -1) {
                    version = nAgt.substring(verOffset + 8);
                }
            }
            // MSIE
            else if ((verOffset = nAgt.indexOf('MSIE')) !== -1) {
                browser = 'Microsoft Internet Explorer';
                version = nAgt.substring(verOffset + 5);
            }
            // MSIE
            else if ((verOffset = nAgt.indexOf('Edge')) !== -1) {
                browser = 'Microsoft Edge';
                version = nAgt.substring(verOffset + 5);
            }
            //IE 11 no longer identifies itself as MS IE, so trap it
            //http://stackoverflow.com/questions/17907445/how-to-detect-ie11
            else if ((browser === 'Netscape') && (nAgt.indexOf('Trident/') !== -1)) {

                browser = 'Microsoft Internet Explorer';
                version = nAgt.substring(verOffset + 5);
                if ((verOffset = nAgt.indexOf('rv:')) !== -1) {
                    version = nAgt.substring(verOffset + 3);
                }

            }

            // Chrome
            else if ((verOffset = nAgt.indexOf('Chrome')) !== -1) {
                browser = 'Chrome';
                version = nAgt.substring(verOffset + 7);
            }
            // Safari
            else if ((verOffset = nAgt.indexOf('Safari')) !== -1) {
                browser = 'Safari';
                version = nAgt.substring(verOffset + 7);
                if ((verOffset = nAgt.indexOf('Version')) !== -1) {
                    version = nAgt.substring(verOffset + 8);
                }

                // Chrome on iPad identifies itself as Safari. Actual results do not match what Google claims
                //  at: https://developers.google.com/chrome/mobile/docs/user-agent?hl=ja
                //  No mention of chrome in the user agent string. However it does mention CriOS, which presumably
                //  can be keyed on to detect it.
                if (nAgt.indexOf('CriOS') !== -1) {
                    //Chrome on iPad spoofing Safari...correct it.
                    browser = 'Chrome';
                    //Don't believe there is a way to grab the accurate version number, so leaving that for now.
                }
            }
            // Firefox
            else if ((verOffset = nAgt.indexOf('Firefox')) !== -1) {
                browser = 'Firefox';
                version = nAgt.substring(verOffset + 8);
            }
            // Other browsers
            else if ((nameOffset = nAgt.lastIndexOf(' ') + 1) < (verOffset = nAgt.lastIndexOf('/'))) {
                browser = nAgt.substring(nameOffset, verOffset);
                version = nAgt.substring(verOffset + 1);
                if (browser.toLowerCase() === browser.toUpperCase()) {
                    browser = navigator.appName;
                }
            }
            // trim the version string
            if ((ix = version.indexOf(';')) !== -1) {
                version = version.substring(0, ix);
            }
            if ((ix = version.indexOf(' ')) !== -1) {
                version = version.substring(0, ix);
            }
            if ((ix = version.indexOf(')')) !== -1) {
                version = version.substring(0, ix);
            }

            majorVersion = parseInt('' + version, 10);
            if (isNaN(majorVersion)) {
                version = '' + parseFloat(navigator.appVersion);
                majorVersion = parseInt(navigator.appVersion, 10);
            }

            for (var id in clientStrings) {
                var cs = clientStrings[id];
                if (cs.r.test(nAgt)) {
                    os = cs.s;
                    break;
                }
            }

            if (/Windows/.test(os)) {
                osVersion = /Windows (.*)/.exec(os)[1];
                os = 'Windows';
            }

            switch (os) {
                case 'Mac OS X':
                    osVersion = /Mac OS X (10[\.\_\d]+)/.exec(nAgt)[1];
                    break;

                case 'Android':
                    osVersion = /Android ([\.\_\d]+)/.exec(nAgt)[1];
                    break;

                case 'iOS':
                    osVersion = /OS (\d+)_(\d+)_?(\d+)?/.exec(nVer);
                    osVersion = osVersion[1] + '.' + osVersion[2] + '.' + (osVersion[3] | 0);

                    osVersion = /OS (\d+)_(\d+)_?(\d+)?/.exec(nVer);
                    device = navigator.userAgent.match(/iPad/i);
                    if (device === null) {
                        device = navigator.userAgent.match(/iPhone/i);
                        if (device === null) {
                            device = navigator.userAgent.match(/iPod/i);
                        }
                    }
                    device = device[0];
                    break;

            }

            return {
                screen: screenSize,
                browser: browser,
                device: device,
                browserVersion: majorVersion,
                mobile: mobile,
                os: os,
                osVersion: osVersion
            };
        }

        return {
            getOS: getOS
        };
    });

