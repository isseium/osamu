exports.right = function(){
    $.right_mark.show();
    $.wrong_mark.hide();
};

exports.wrong = function(){
    $.right_mark.hide();
    $.wrong_mark.show();
};

exports.setDescription = function(text){
    $.description.text = text;
};

function closeWindow(){
    $._resultWindow.close($._resultWindow);
};
