var args = arguments[0] || {};

$.image.image = args.image_url;
$.name.text = args.name;

exports.getDashboardItem = function(){
    return $.badge_item;
};
