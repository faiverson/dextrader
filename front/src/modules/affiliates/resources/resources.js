angular.module('app.affiliates-resources', [])
    .factory('AffiliateResources', [function(){
        return [
            {
                "name": "FB Wall Posts",
                "id": "aff-res-fbw",
                "items": [
                    {
                        "title": "Short Email #1:",
                        "subtitle": "I really, really like you",
                        "templateUrl": "modules/affiliates/resources/fb-wall-posts/short-email-1.tpl.html",
                        "isDefault": true
                    },
                    {
                        "title": "Short Email #2:",
                        "subtitle": "TRUTH",
                        "templateUrl": "modules/affiliates/resources/fb-wall-posts/short-email-2.tpl.html"
                    },
                    {
                        "title": "Short Email #3:",
                        "subtitle": "If you’re already RICH… don’t open this email",
                        "templateUrl": "modules/affiliates/resources/fb-wall-posts/short-email-3.tpl.html"
                    }
                ]
            },
            {
                "name": "FB Private Messages",
                "id": "aff-res-fbm",
                "items": [
                    {
                        "title": "Short Email #1:",
                        "subtitle": "I really, really like you",
                        "templateUrl": "modules/affiliates/resources/fb-private-messages/short-email-1.tpl.html",
                        "isDefault": true
                    },
                    {
                        "title": "Short Email #2:",
                        "subtitle": "TRUTH",
                        "templateUrl": "modules/affiliates/resources/fb-private-messages/short-email-2.tpl.html"
                    },
                    {
                        "title": "Short Email #3:",
                        "subtitle": "If you’re already RICH… don’t open this email",
                        "templateUrl": "modules/affiliates/resources/fb-private-messages/short-email-3.tpl.html"
                    }
                ]
            },
            {
                "name": "Long Emails",
                "id": "aff-res-lem",
                "items": [
                    {
                        "title": "Short Email #1:",
                        "subtitle": "I really, really like you",
                        "templateUrl": "modules/affiliates/resources/long-emails/short-email-1.tpl.html",
                        "isDefault": true
                    },
                    {
                        "title": "Short Email #2:",
                        "subtitle": "TRUTH",
                        "templateUrl": "modules/affiliates/resources/long-emails/short-email-2.tpl.html"
                    },
                    {
                        "title": "Short Email #3:",
                        "subtitle": "If you’re already RICH… don’t open this email",
                        "templateUrl": "modules/affiliates/resources/long-emails/short-email-3.tpl.html"
                    }
                ]
            },
            {
                "name": "Short Emails",
                "id": "aff-res-sem",
                "items": [
                    {
                        "title": "Short Email #1:",
                        "subtitle": "I really, really like you",
                        "templateUrl": "modules/affiliates/resources/short-emails/short-email-1.tpl.html",
                        "isDefault": true
                    },
                    {
                        "title": "Short Email #2:",
                        "subtitle": "TRUTH",
                        "templateUrl": "modules/affiliates/resources/short-emails/short-email-2.tpl.html"
                    },
                    {
                        "title": "Short Email #3:",
                        "subtitle": "If you’re already RICH… don’t open this email",
                        "templateUrl": "modules/affiliates/resources/short-emails/short-email-3.tpl.html"
                    }
                ]
            },
            {
                "name": "Banners",
                "id": "aff-res-ban",
                "items": [
                    {
                        "title": "Short Email #1:",
                        "subtitle": "I really, really like you",
                        "templateUrl": "modules/affiliates/resources/banners/short-email-1.tpl.html",
                        "isDefault": true
                    },
                    {
                        "title": "Short Email #2:",
                        "subtitle": "TRUTH",
                        "templateUrl": "modules/affiliates/resources/banners/short-email-2.tpl.html"
                    },
                    {
                        "title": "Short Email #3:",
                        "subtitle": "If you’re already RICH… don’t open this email",
                        "templateUrl": "modules/affiliates/resources/banners/short-email-3.tpl.html"
                    }
                ]
            }
        ];
    }]);
