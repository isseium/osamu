var apiMapper = new ApiMapper();

// Loading 画面 
var loadingWindow = Alloy.createController('_loadingWindow').getView();

// 結果画面 
var resultController = Alloy.createController('_resultWindow');

// 正答番号を保持する変数
var right_answer = 0;

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

            // 問題文セット
            $.question.text = response.Question.question;

            // 選択肢セット
            $.option1.title = "1. " + response.Question.option1;
            $.option2.title = "2. " + response.Question.option2;
            $.option3.title = "3. " + response.Question.option3;
            $.option4.title = "4. " + response.Question.option4;

            // 正答セット
            right_answer = response.Question.right_answer;

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
function score_anser(e){
    if(e.source.titleid == right_answer){
        resultController.right();
    }else{
        resultController.wrong();
    }

    var resultWindow = resultController.getView();
    resultWindow.addEventListener('close', nextQuestion);
    resultWindow.open();
};
