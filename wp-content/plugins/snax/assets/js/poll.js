/* global jQuery */
/* global document */
/* global snax_polls */
/* global snax_poll_config */
/* global JSON */
/* global console */

if ( typeof window.snax_polls === 'undefined' ) {
    window.snax_polls = {};
}

/**
 * Helpers.
 */
(function($, ctx) {

    ctx.shuffleArray = function(array) {
        var currentIndex = array.length;
        var randomIndex;
        var tempValue;

        while (currentIndex > 0) {
            randomIndex = Math.floor(Math.random() * currentIndex);
            currentIndex -= 1;

            // And swap it with the current element.
            tempValue = array[currentIndex];
            array[currentIndex] = array[randomIndex];
            array[randomIndex] = tempValue;
        }

        return array;
    };

})(jQuery, snax_polls);

/**
 * Handle poll actions.
 */
(function ($, ctx) {

    'use strict';

    // Poll object.
    var poll;

    // Poll DOM element.
    var $poll;

    // CSS classes.
    var QUESTION_ANSWERED       = 'snax-poll-question-answered';
    var QUESTION_UNANSWERED     = 'snax-poll-question-unanswered';
    var QUESTION_REVEAL_ANSWERS = 'snax-poll-question-reveal-answers';
    var QUESTION_HIDDEN         = 'snax-poll-question-hidden';
    var ANSWER_CHECKED          = 'snax-poll-answer-checked';
    var PAGINATION_LOCKED       = 'g1-arrow-disabled';

    var pollResults;

    var bindEvents = function() {
        pollStartedAction();
        questionAnsweredAction();
        viewResultsAction();
        hideResultsAction();
        nextPageAction();

        poll.on('questionAnswered', function(questionId, answerId, results) {
            pollResults = results;

            var $question = $poll.find('.snax-poll-question-' + questionId);
            var $answer   = $poll.find('.snax-poll-answer-' + answerId);

            // Reveal answers.
            if ('standard' === poll.revealCorrectWrongAnswers()) {
                viewQuestionResults($question);
            }
        });

        poll.on('answerSelected', function(questionId, answerId, onInit) {
            var $question = $poll.find('.snax-poll-question-' + questionId);
            var $answer   = $question.find('.snax-poll-answer-' + answerId);

            // Mark as voted.
            $question.removeClass(QUESTION_UNANSWERED).addClass(QUESTION_ANSWERED);
            $answer.addClass(ANSWER_CHECKED);

            // Hide effect if answer selected on init.
            if ('versus' === poll.getType() || 'binary' === poll.getType()) {
                if (onInit || 'none' === poll.revealCorrectWrongAnswers()) {
                    $question.find('.snax-poll-anticipation').hide();
                }
            }

            // Show share buttons.
            showShareLinks($question);

            // Enable pagination.
            if (!$question.is('.snax-poll-question-hidden')) {
                $poll.find('.snax-poll-pagination .g1-arrow-disabled').removeClass('g1-arrow-disabled');
            }
        });

        poll.on('started', function () {
            $poll.find('.snax-poll-with-start-trigger').removeClass('snax-poll-with-start-trigger');
        });
    };

    var pollStartedAction = function() {
        $poll.find('.snax-poll-button-start-poll').on('click', function(e) {
            e.preventDefault();

            poll.start();
        });
    };

    var questionAnsweredAction = function() {
        $poll.find('.snax-poll-question').on('click', function(e) {
            e.preventDefault();

            var $question = $(this);

            var $answer = $(e.target).parents('.snax-poll-answer');

            // Proceed only if user clicked in answer.
            if (!$answer.is('.snax-poll-answer')) {
                return;
            }

            var answerId    = parseInt($answer.attr('data-quizzard-answer-id'), 10);
            var questionId  = parseInt($question.attr('data-quizzard-question-id'), 10);

            if ('versus' === poll.getType() && !$question.hasClass(QUESTION_ANSWERED)) {
                $question.find('.snax-poll-anticipation').show();
            }

            poll.addAnswer(questionId, answerId, 1);
        });
    };

    var viewResultsAction = function () {
        $poll.on('click', '.snax-action-poll-view-results', function(e) {
            e.preventDefault();

            var $question = $(e.target).parents('.snax-poll-question');

            if ($question.hasClass(QUESTION_REVEAL_ANSWERS)) {
                throw 'Results already loaded.';
            }

            var questionId  = parseInt($question.attr('data-quizzard-question-id'), 10);

            poll.getResults(function (results) {
                pollResults = results;

                viewQuestionResults($question);
            });
        });
    };

    var hideResultsAction = function () {
        $poll.on('click', '.snax-action-poll-hide-results', function(e) {
            e.preventDefault();

            var $question = $(e.target).parents('.snax-poll-question');

            if (!$question.hasClass(QUESTION_REVEAL_ANSWERS)) {
                throw 'Results was not loaded. Nothing to hide';
            }

            $poll.find('.snax-poll-answers').show();
            $poll.find('.snax-poll-answers-with-results').hide();

            $question.removeClass(QUESTION_REVEAL_ANSWERS);

            showViewResultsLink($question);
        });
    };

    var nextPageAction = function() {
        $poll.find('.snax-poll-pagination-next').on('click', function(e) {
            if ($(this).hasClass(PAGINATION_LOCKED)) {
                e.preventDefault();
            }

        });
    };

    var viewQuestionResults = function($question) {
        if ( $question.hasClass(QUESTION_REVEAL_ANSWERS) ){
            return;
        }

        var questionId = parseInt($question.attr('data-quizzard-question-id'), 10);

        // Show all users' answers (%).
        var tweenFont = function( t, b, c, d ) {
            return c * Math.sin(t/d * (Math.PI/2)) + b;
        };

        var i18n = $.parseJSON(snax_poll_config).i18n;
        var answers = typeof pollResults.questions[questionId] !== 'undefined' ? pollResults.questions[questionId] : { 'answers': {}, 'total': 0 };

        // Clean up previous results.
        $question.find('.snax-poll-answers-with-results').remove();

        var $answers = $question.find('.snax-poll-answers');
        var $results = $answers.clone();
        $results.addClass('snax-poll-answers-with-results');

        $results.insertAfter($answers);
        $answers.hide();

        $results.find('.snax-poll-answer').each(function () {
            var $answer = $(this);
            var answerId = parseInt($answer.attr('data-quizzard-answer-id'), 10);

            var amount = 0;

            if (typeof answers.answers[answerId] !== 'undefined') {
                amount = answers.answers[answerId];
            }

            var percentage  = answers.total > 0 ? Math.round( amount / answers.total * 100 ) : 0;
            var percentageSize = percentage + 50;
            var percentageClass = 'snax-poll-answer-percentage-higher';
            if (percentage < 50 ) {
                percentageClass = 'snax-poll-answer-percentage-lower';
            }

            var fontSize = tweenFont(percentage, 16, 40, 100);
            var amountText = amount;
            if (amountText > 1000 && amountText < 10000) {
                amountText = parseInt(amountText,10) / 1000;
                amountText = + amountText.toFixed(2);
                amountText += i18n.k;
            }
            if (amountText > 10000) {
                amountText = parseInt(amountText,10) / 1000;
                amountText = + amountText.toFixed(1);
                amountText += i18n.k;
            }

            if ('classic' === poll.getType()) {
                $answer.prepend('<div class="snax-poll-answer-results"><div class="snax-poll-answer-results-percentage">' + percentage + '%</div><div class="snax-poll-answer-results-amount">' + amountText + ' ' + i18n.votes +'</div></div>');
                $answer.find('.snax-poll-answer-label').prepend('<div class="snax-poll-answer-percentage"><div style="width: '+ percentage +'%;"></div></div>');
                $answer.parent('.snax-poll-answers-item').attr('data-snax-percentage',percentage);
            }

            if ('versus' === poll.getType()) {
                $question.removeClass('snax-poll-question-unanswered');
                $question.find('.snax-poll-anticipation').hide();

                $answer.find('.snax-poll-answer-media').append('<div class="snax-poll-answer-percentage ' + percentageClass + ' " style = "font-size:'+  fontSize +'px"><div style="height: '+ percentageSize +'%;width: '+ percentageSize +'%;">' + percentage + '%</div></div>');

                // Prevent voting when results shown.
                $answer.on('click', function (e) {
                    e.stopImmediatePropagation();
                });
            }

            if ('binary' === poll.getType()) {
                $question.removeClass('snax-poll-question-unanswered');
                $question.find('.snax-poll-anticipation').remove();

                var binaryResultClass = 'snax-poll-binary-result-' + questionId;
                var binarySize = tweenFont(percentage, 20, 100, 100);
                if ( $poll.find('.' + binaryResultClass).length === 0 ){
                    $poll.find('.poll-binary .snax-poll-question-' + questionId + ' .snax-poll-question-media').append('<div class="snax-poll-binary-result ' + binaryResultClass + '"></div>');
                }
                $poll.find('.' + binaryResultClass).append('<div class="snax-poll-answer-percentage ' + percentageClass + ' " style = "font-size:'+  fontSize +'px;"><div style="height:' + binarySize + 'px;width:' + binarySize + 'px;">' + percentage + '%</div></div>');

                // Prevent voting when results shown.
                $answer.on('click', function (e) {
                    e.stopImmediatePropagation();
                });
            }
        });

        if ('classic' === poll.getType()) {
            // Sort answers.
            var $li = $results.find('.snax-poll-answers-item');
            var $ul = $results.find('.snax-poll-answers-items');

            $li.sort(function (a, b) {
                var contentA =parseInt( $(a).attr('data-snax-percentage'));
                var contentB =parseInt( $(b).attr('data-snax-percentage'));
                return (contentA > contentB) ? -1 : (contentA < contentB) ? 1 : 0;
            });

            $li.detach().appendTo($ul);
        }

        // Mark results as shown.
        $question.addClass(QUESTION_REVEAL_ANSWERS);

        if ($question.is(QUESTION_ANSWERED)) {
            hideResultsLinks($question);
        } else {
            showHideResultsLink($question);
        }

        // Scroll to the top of the results.
        // $results.get(0).scrollIntoView();
    };

    var showViewResultsLink = function($question) {
        var $toggleResults = $question.find('.snax-poll-toggle-results');

        if ($toggleResults.length === 0) {
            return;
        }

        $toggleResults.removeClass('snax-poll-toggle-results-inactive').addClass('snax-poll-toggle-results-active');
        $toggleResults.find('.snax-action-poll-view-results').removeClass('snax-action-hidden');
        $toggleResults.find('.snax-action-poll-hide-results').addClass('snax-action-hidden');

        if ('versus' === poll.getType()) {
            // If question not answered, allow voting.
            if (!$question.hasClass('snax-poll-question-answered')) {
                $question.addClass('snax-poll-question-unanswered');
            }
        }

        if ('binary' === poll.getType()) {
            // If question not answered, allow voting.
            if (!$question.hasClass('snax-poll-question-answered')) {
                $question.addClass('snax-poll-question-unanswered');
            }

            $question.find('.snax-poll-binary-result').remove();
        }
    };

    var showHideResultsLink = function($question) {
        var $toggleResults = $question.find('.snax-poll-toggle-results');

        if ($toggleResults.length === 0) {
            return;
        }

        $toggleResults.removeClass('snax-poll-toggle-results-inactive').addClass('snax-poll-toggle-results-active');
        $toggleResults.find('.snax-action-poll-view-results').addClass('snax-action-hidden');

        if ($question.hasClass(QUESTION_ANSWERED)) {
            $toggleResults.find('.snax-poll-question-answered').removeClass('snax-action-hidden');
            $toggleResults.find('.snax-poll-question-not-answered').addClass('snax-action-hidden');
        } else {
            $toggleResults.find('.snax-poll-question-answered').addClass('snax-action-hidden');
            $toggleResults.find('.snax-poll-question-not-answered').removeClass('snax-action-hidden');
        }
    };

    var hideResultsLinks = function($question) {
        var $toggleResults = $question.find('.snax-poll-toggle-results');

        if ($toggleResults.length === 0) {
            return;
        }

        $toggleResults.removeClass('snax-poll-toggle-results-active').addClass('snax-poll-toggle-results-inactive');
    };

    var showShareLinks = function($question) {
        var $shareLinks = $question.find('.snax-poll-share-links');

        var $checkedAnswer = $question.find('.snax-poll-answer-checked:visible');

        if ($checkedAnswer.length === 0) {
            return;
        }

        var answerText = $.trim($checkedAnswer.find('.snax-poll-answer-label-text').text());

        // Replace placeholders.
        $shareLinks.find('a').each(function () {
            var $a = $(this);

            // Replace href.
            var href = $a.attr('href');
            href = href.replace( 'MY_CHOICE', encodeURIComponent(answerText) );
            $a.attr('href', href);

            // Replace data-share-text.
            var shareText = $a.attr('data-share-text');

            if (shareText) {
                shareText = shareText.replace( 'MY_CHOICE', answerText);
                $a.attr('data-share-text', shareText);
            }
        });

        // Show links.
        $shareLinks.removeClass('snax-poll-share-links-inactive').addClass('snax-poll-share-links-active');
    };

    ctx.initPoll = function () {
        $poll = $('.snax > .poll').parent();

        $poll.addClass('snax-share-object');

        if ($poll.length === 0) {
            return;
        }

        // Create poll object.
        var config = $.parseJSON(snax_poll_config);

        poll = new ctx.Poll(config);

        // Store reference.
        $poll.data('quizzardShareObject', poll);

        var questionIds = poll.getActiveQuestions();    // It can be just a subset of all questions (shuffle: on and questions per poll: < all questions).
        var questions = [];                             // Array of DOM objects representing questions.

        $.each(questionIds, function(index, id) {
            var $question = $poll.find('.snax-poll-question-' + id);
            questions.push($question.parent());

            if (poll.shuffleAnswers()) {
                // Get all question's answers.
                var answers = $question.find('.snax-poll-answers-item');

                // Shuffle them.
                ctx.shuffleArray(answers);

                // Reorder answers in DOM.
                $question.find('.snax-poll-answers-items').append(answers);
            }
        });

        // Reorder questions in DOM.
        $poll.find('.snax-poll-questions-items').html(questions);

        var total = questions.length;

        // Show question(s).
        if (poll.oneQuestionPerPage()) {
            var index = poll.getPage() - 1;

            // Calculate xofy.
            questions[index].find('.snax-poll-question-xofy-y').html( total );

            // Calculate progress.
            var percentage  = (index + 1) / total * 100;
            questions[index].find('.snax-poll-question-progress-bar').width( percentage + '%' );

            // Show.
            questions[index].find('.snax-poll-question').removeClass(QUESTION_HIDDEN);
        } else {
            $.each(questions, function(index, $question) {
                // Calculate xofy.
                $question.find('.snax-poll-question-xofy-y').html( total );

                // Calculate progress.
                var percentage  = (index + 1) / total * 100;
                $question.find('.snax-poll-question-progress-bar').width( percentage + '%' );

                // Show.
                $question.find('.snax-poll-question').removeClass(QUESTION_HIDDEN);
            });
        }

        // Do not show results.
        if ('none' === poll.revealCorrectWrongAnswers()) {
            $poll.find('.snax-poll-toggle-results').remove();
        }

        bindEvents();

        poll.initAnswers();
    };

    // Init.
    $(document).ready(function() {
        ctx.initPoll();
    });

})(jQuery, snax_polls);

/**
 * Define Poll class.
 */
(function($, ctx) {

    'use strict';

    ctx.Poll = function(options) {
        var obj = {};
        var defaults = {
            debug: false
        };

        var currentPage;
        var activeQuestions;
        var answeredQuestions;
        var correctAnswers;
        var answers;
        var events;
        var correct_answers = {};

        // Constructor.
        var init = function () {
            options = $.extend(defaults, options);

            for (var i = 0; i < options.questions_answers_arr.length; i++) {
                var item = options.questions_answers_arr[i];

                correct_answers[item.question_id] = item.answer;
            }

            log(options);

            currentPage = options.page;

            // Register default callbacks.
            events = {
                'started':          function() {},
                'ended':            function() {},
                'questionAnswered': function() {},
                'answerSelected':   function() {}
            };

            return obj;
        };

        // Public API.

        obj.getType = function() {
            return options.poll_type;
        };

        obj.initAnswers = function() {
            log('Init answers');

            correctAnswers      = 0;    // Number of correct answers.
            answers             = {};   // Answer list (question id => answer id).
            answeredQuestions   = 0;    // Number of question that were already answered.

            var multipleVoting = 1 !== parseInt(options.one_vote_per_user, 10);

            log( 'Multiple voting: ' + ( multipleVoting ? 'ON' : 'OFF' ) );

            if ( ! multipleVoting ) {
                // Read stored answers and select them.
                var userAnswers = readFromLocalStorage('answers');

                if (null !== userAnswers) {
                    for (var questionId in userAnswers) {
                        var answerId = userAnswers[questionId];

                        selectAnswer(questionId, answerId, true);
                    }
                }
            }

            // Hide "Let's Play" button.
            if (obj.oneQuestionPerPage() && 1 === currentPage) {
                var questionsOnPage   = obj.getActiveQuestions();
                var currentQuestionId = questionsOnPage[0];

                if (wasQuestionAnswered(currentQuestionId)) {
                    obj.start();
                }
            }
        };

        obj.getActiveQuestions = function() {
            if (activeQuestions) {
                return activeQuestions;
            }

            // When we shuffle questions, we need to keep the same state over all pages.
            var keepStateBetweenPages = obj.shuffleQuestions() && obj.oneQuestionPerPage();

            if (keepStateBetweenPages) {
                if (1 === currentPage) {
                    resetLocalStorage();
                }

                activeQuestions = readFromLocalStorage('active_questions');
            }

            if (!activeQuestions) {
                log('Build final poll question list');
                activeQuestions = [];

                // All questions, in original order.
                for ( var i = 0; i < options.questions_answers_arr.length; i++ ) {
                    var item = options.questions_answers_arr[i];

                    activeQuestions.push(item.question_id);
                }

                log('Active questions');
                log(activeQuestions);

                if (obj.shuffleQuestions()) {
                    ctx.shuffleArray(activeQuestions);

                    log('Shuffled questions');
                    log(activeQuestions);

                    if (-1 !== options.questions_per_poll) {
                        limitQuestions();

                        log('Limited questions');
                        log(activeQuestions);
                    }
                }

                if (keepStateBetweenPages) {
                    addToLocalStorage('active_questions', activeQuestions);
                }
            }

            return activeQuestions;
        };

        obj.addAnswer = function(questionId, answerId, points) {
            // Proceed only if question selected.
            if (!selectAnswer(questionId, answerId)) {
                return;
            }

            log('Question ' + questionId + ' answered (answer ' + answerId + ').');

            // Update user's answers.
            var userAnswers = readFromLocalStorage('answers');

            if (!userAnswers) {
                userAnswers = {};
            }

            userAnswers[questionId] = answerId;

            var ttl = options.user_votes_expire_time;

            addToLocalStorage('answers', userAnswers, ttl);

            saveAnswer(questionId, answerId, function(results) {
                events.questionAnswered(questionId, answerId, results);

                if (answeredQuestions === activeQuestions.length) {
                    log('Poll ended.');

                    events.ended();
                }
            }, points);
        };

        obj.start = function() {
            events.started();
        };

        obj.getAnswer = function(questionId) {
            return answers[questionId];
        };

        obj.getCorrectAnswer = function(questionId) {
            return correct_answers[questionId];
        };

        obj.revealCorrectWrongAnswers = function() {
            return options.reveal_correct_wrong_answers;
        };

        obj.shuffleQuestions = function() {
            return options.shuffle_questions;
        };

        obj.shuffleAnswers = function() {
            return options.shuffle_answers;
        };

        obj.oneQuestionPerPage = function() {
            return options.one_question_per_page;
        };

        obj.isCorrectAnswer = function(questionId, answerId) {
            return answerId === correct_answers[questionId];
        };

        obj.on = function(eventName, callback) {
            events[eventName] = callback;
        };

        obj.getScore = function(type) {
            var correct = correctAnswers;
            var all     = activeQuestions.length;
            var score   = '';

            switch(type) {
                case 'percentage':
                    score = Math.round(correct / all * 100);
                    break;
            }

            return score;
        };

        obj.getPage = function() {
            return currentPage;
        };

        obj.getResults = function (callback) {
            log('Get question results.');

            var xhr = $.ajax({
                'type': 'GET',
                'url': options.ajax_url,
                'dataType': 'json',
                'data': {
                    'action':       'snax_get_poll_results',
                    'poll_id':      options.poll_id,
                }
            });

            xhr.done(function (res) {
                if (res.status === 'success') {
                    callback(res.args.results);
                }
            });
        };

        // Private scope.

        var selectAnswer = function(questionId, answerId, onInit) {
            // Proceed only if question is not answered yet.
            if (wasQuestionAnswered(questionId)) {
                return false;
            }

            log( 'Select answer ' + answerId + ' for question ' + questionId );

            // Update state.
            answeredQuestions++;

            answers[questionId] = answerId;

            if (obj.isCorrectAnswer(questionId, answerId)) {
                correctAnswers++;
            }

            events.answerSelected(questionId, answerId, onInit);

            return true;
        };

        var wasQuestionAnswered = function(questionId) {
            return typeof answers[questionId] !== 'undefined';
        };

        var limitQuestions = function() {
            var questionsLimit = Math.min(options.questions_per_poll, options.all_questions);

            if (questionsLimit !== activeQuestions.length) {
                activeQuestions.splice(questionsLimit, activeQuestions.length - questionsLimit);
            }
        };

        var saveAnswer = function(questionId, answerId, callback, points) {
            log('Save answer.');

            var xhr = $.ajax({
                'type': 'POST',
                'url': options.ajax_url,
                'dataType': 'json',
                'data': {
                    'action':       'snax_save_poll_answer',
                    'poll_id':      options.poll_id,
                    'author_id':    options.author_id,
                    'question_id':  questionId,
                    'answer_id':    answerId,
                    'summary':      options.share_description,
                    'add_points':   points
                }
            });

            xhr.done(function (res) {
                if (res.status === 'success') {
                    callback(res.args.results);
                }
            });
        };

        var readFromLocalStorage = function(id) {
            log('Reading "'+ id +'" from local storage');

            // Build final var id.
            id = 'snax_poll_'+ options.poll_id + '_' + id;

            var item = localStorage.getItem(id);
            var value = null;

            if (item !== null) {
                log('Value set');
                item = $.parseJSON(item);

                // Check if expired.
                if (item.expire > 0) {
                    log('Expire time set, TTL: ' + item.expire);

                    var now = new Date();

                    // Value expired.
                    if (now.getTime() > item.expire) {
                        localStorage.removeItem(id);
                        value = null;

                        log('Value expired and removed');
                    } else {
                        value = item.value;

                        log('Value is valid');
                    }

                } else {
                    log('Expire time not set');

                    value = item.value;
                }
            } else {
                log('Value not set');
            }

            log('Value: ');
            log(value);

            return value;
        };

        var addToLocalStorage = function(id, value, ttl) {
            if (typeof ttl !== 'undefined') {
                ttl = parseInt(ttl, 10);
            } else {
                ttl = 0;
            }

            log('Adding "'+ id +'" to local storage (TTL: '+ ttl +')');
            log(value);

            var now = new Date();

            var item = {
                value: value,
                expire: ttl > 0 ? now.getTime() + ttl : 0
            };

            // Build final var id.
            id = 'snax_poll_'+ options.poll_id + '_' + id;

            localStorage.setItem(id, JSON.stringify(item));
        };

        var resetLocalStorage = function() {
            log('Resetting local storage:');

            localStorage.removeItem('snax_poll_' + options.poll_id + '_active_questions');
            log('"active_questions" removed');
        };

        var log = function(data) {
            if (inDebugMode() && typeof console !== 'undefined') {
                console.log(data);
            }
        };

        var inDebugMode = function() {
            return options.debug;
        };

        return init();
    };

})(jQuery, snax_polls);
