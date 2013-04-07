// The contents of this file will be executed before any of
// your view controllers are ever executed, including the index.
// You have access to all functionality on the `Alloy` namespace.
//
// This is a great place to do any initialization for your app
// or create any global variables/functions that you'd like to
// make available throughout your app. You can easily make things
// accessible globally by attaching them to the `Alloy.Globals`
// object. For example:
//
// Alloy.Globals.someGlobalFunction = function(){};

// アプリケーション設定
Alloy.Globals.app = new Object();
Alloy.Globals.app.img_endpoint = "http://osamu-test.ap01.aws.af.cm/";
Alloy.Globals.app.api_endpoint = "http://osamu-test.ap01.aws.af.cm/rsk/zishin/";

// ユーザ設定
Alloy.Globals.user = new Object();
Alloy.Globals.user.user_id = 'n0bisuke';        // TODO: あとで変える

/**
 * ライブラリロード
 */
var ApiMapper = require("apiMapper").ApiMapper;
