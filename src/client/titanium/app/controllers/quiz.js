var apiMapper = new ApiMapper();

exports.setNavigation = function(nav, current){
    $.nav = nav;
    $.current = current;
}

// Loading 画面 
var loadingWindow = Alloy.createController('_loadingWindow').getView();

// 結果画面 
var resultController = Alloy.createController('_resultWindow');

// 問題情報を管理する変数
var question;

// 問題を出題する関数
var nextQuestion = function(){
    loadingWindow.open();

    // Quiz を取得
    apiMapper.question(
        {
            user_id: Alloy.Globals.user.user_id 
        },
        function(e){
            var response = JSON.parse(this.responseText);

            // 問題終了チェック
            if(response.Question == "finish"){
                var dialog = Titanium.UI.createAlertDialog();
                dialog.setTitle('全問正解しました');
                dialog.setMessage('震災の記憶を忘れず、復興をすすめていきましょう！'); 
                dialog.show();

                // インディケータクローズ
                loadingWindow.close();
                $.nav.close($.getView());

                return;
            }

            // 問題文セット
            $.question.text = response.Question.question;

            // 選択肢セット
            $.option1.title = "1. " + response.Question.option1;
            $.option2.title = "2. " + response.Question.option2;
            $.option3.title = "3. " + response.Question.option3;
            $.option4.title = "4. " + response.Question.option4;

            // 問題情報セット 
            question = response.Question;

            // 解説セット
            resultController.setDescription(response.Question.description);

            // インディケータクローズ
            loadingWindow.close();
        },
        function(e){
            alert(this.responseText);
        }
    );
}

// 問題を出題する
nextQuestion();


// 回答について採点する関数
function score_answer(e){
    if(e.source.titleid == question.right_answer){
        resultController.right();
    }else{
        resultController.wrong();
    }
    
    // サーバに送信
    apiMapper.answer(
        {
            user_id: Alloy.Globals.user.user_id,
            question_id: question.id,
            option: e.source.titleid,
        },
        function(){
            // 結果を表示
            var resultWindow = resultController.getView();
            resultWindow.removeEventListener('close', nextQuestion);
            resultWindow.addEventListener('close', nextQuestion);
            resultWindow.open();
        },
        function(){
            alert('通信エラーが発生しました');
        }
    );
};
