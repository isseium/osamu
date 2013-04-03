// navbar の設定
var button_badges = Ti.UI.createButton({title:'Badges'});
var button_start_quiz = Ti.UI.createButton({title:'Quiz'});

button_badges.addEventListener('click', function(){
    var window_badges = Alloy.createController('badges', {}).getView();
    $.nav.open(window_badges);
});

button_start_quiz.addEventListener('click', function(){
    var window_badges = Alloy.createController('quiz', {}).getView();
    $.nav.open(window_badges);
});

$.win.leftNavButton = button_badges;
$.win.rightNavButton = button_start_quiz;

// View 表示
$.index.open();
