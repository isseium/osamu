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

// アプリケーション個別設定を読み込み
Ti.include("app_config.js");

// ユーザ設定
Alloy.Globals.user = new Object();

// グローバル設
Alloy.Globals.app.ymap = {
    lat: 38.2,
    lon: 140.6,
    zoom: 9,
    height: 1600,
    width: 600,
    mode: "blankmap",
    paint_color: "00CC66",
};

/**
 * ライブラリロード
 */
var ApiMapper = require("apiMapper").ApiMapper;
