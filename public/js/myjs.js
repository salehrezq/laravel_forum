$(function () {

    (function notifications() {

        var $notificationsBox = $('.notifications');
        var $notisList = $('.notis-list');
        var $page = $('.page');
        var request_in_progress = false;
        var readScrollYPostion = true;
        var scroll_Y_Position = 0; // Used to reset scrollbar after loading data on scroll event
        var unreadNotificationsCount = 1;
        var counter = 0;
        //
        var $notiCountEl = $('.noti-counter');

        var notiCount = $('.noti-count').val();
        if (notiCount > 0) {
            $notiCountEl
                    .css({opacity: 0})
                    .text(notiCount)
                    .css({top: '-10px'})
                    .animate({top: '-2px', opacity: 1}, 500);
        }

        $('.btn-notify').on('click', function () {

            $notificationsBox.fadeToggle(100, 'linear', function () {
                if ($notificationsBox.is(':hidden')) {
                    resetAfterRelease();
                    $(this).addClass('btn-noti-released').removeClass('btn-noti-clicked');
                } else {
                    fetchNotifications(); // intial notifications load
                    $(this).addClass('btn-noti-clicked').removeClass('btn-noti-released');
                }
            });

            $notiCountEl.fadeOut('slow'); // Hide the counter

            return false;
        });

        $(document).on('click', function () {
            $notificationsBox.fadeOut(100);
            if ($notiCountEl.is(':hidden')) {
                resetAfterRelease();
                $('.btn-notify').addClass('btn-noti-released').removeClass('btn-noti-clicked');
            }
        });

        function resetAfterRelease() {
            $notisList.empty();
            $page.val(0);
            unreadNotificationsCount = 1;
            counter = 0;
        }

        $notificationsBox.on('click', function () {
            return false;       // Do nothing when container is clicked
        });

        function fetchNotifications() {

            if (counter >= unreadNotificationsCount) {
                return;
            }

            if (request_in_progress === true) {
                return;
            }

            request_in_progress = true;

            var page = $page.val();
            var next_page = parseInt(page) + 1;

            axios.get(`/notifications/${next_page}`).then((response) => {

                var respData = response.data;

                if (respData.status === true) {

                    $page.val(next_page);

                    unreadNotificationsCount = respData.unreadNotificationsCount; // The count of all unread notifications
                    counter += respData.unreadNotifications.length; // notifications per page
                    listNotifications(respData.unreadNotifications);
                } else {
                    $notisList.append('<h3>No new notifications yet.</h3>');

                }

                request_in_progress = false;
                readScrollYPostion = true;

            }).catch((response) => {
                console.log(response);
            });
        }

        function listNotifications(notis) {

            var length = notis.length;

            if (length > 0) {
                for (var i = 0; i < length; i++) {

                    var data = notis[i].data;

                    var notiItem = '';
                    if (notis[i].type === 'App\\Notifications\\ThreadNotification') {
                        notiItem = repliedOnNotificationTemplate(notis, data, i);
                    } else if (notis[i].type === 'App\\Notifications\\UserMentionNotification') {
                        console.log('UserMentionNotification');
                        notiItem = metionedNotificationTemplate(notis, data, i);
                    }

                    $notisList.append(notiItem);
                    $notisList.scrollTop(scroll_Y_Position);
                }
                // Add margin-top to the first item
                $("[id^='noti-f-']").first().find('.noti-item').addClass('mt');
                // Remove the border-bottom from last item
                $("[id^='noti-f-']").last().find('.noti-item').removeClass('bb');
            }
        }

        function repliedOnNotificationTemplate(notis, data, i) {

            return `<div id="noti-f-${notis[i].id}">
                <p class='noti-item bb'>
                    <a class='a-noti-item' href="/threads/${data.thread_channel_slug}/${data.thread_id}">
                    <span class='noti-name'>${data.author}</span> replied on <span class='noti-thread-title'>${data.thread_title}</span>
                    <abbr class='noti-time' title='${notis[i].created_at}'>${moment(notis[i].created_at).fromNow()}</abbr>
                    </a>
                </p>
            </div>`;
        }

        function metionedNotificationTemplate(notis, data, i) {

            return `<div id="noti-f-${notis[i].id}">
                <p class='noti-item bb'>
                    <a class='a-noti-item' href="/threads/${data.thread_channel_slug}/${data.thread_id}">
                    <span class='noti-name'>${data.author}</span> mentioned you in a comment on <span class='noti-thread-title'>${data.thread_title}</span>
                    <abbr class='noti-time' title='${notis[i].created_at}'>${moment(notis[i].created_at).fromNow()}</abbr>
                    </a>
                </p>
            </div>`;
        }

        $('.notis-list').on('scroll', _.debounce(scrollReaction, 1000));

        function scrollReaction() {

            var fullScrollHeight = $notisList.prop('scrollHeight');
            var contentHeight = $notisList.height();

            if (readScrollYPostion === true) {
                scroll_Y_Position = $notisList.scrollTop();
            }

            if ((fullScrollHeight - scroll_Y_Position) <= (contentHeight)) {
                readScrollYPostion = false;
                fetchNotifications();
            }
        }

        // When a single notification item is clicked
        $('*').on('click', "[id^='noti-f-']", function (e) {
            e.stopImmediatePropagation();
            e.preventDefault();

            var notificationId = $(this).attr('id').substring(7);

            axios.post(`/notifications/markasread/${notificationId}`, {
                _method: 'patch'
            }).then((response) => {
                if (response.data.status === true) {
                    $link = $(this).find('a');
                    window.location.href = $link.attr('href');
                }
            }).catch((response) => {
                console.log(response);
            });
        });


        $('.btn_mark_all_as_read').on('click', function () {

            axios.post('/notifications/markallasread/', {
                _method: 'patch'
            }).then((response) => {
                if (response.data.status === true) {
                    resetAfterRelease();
                }
            }).catch((response) => {
                console.log(response);
            });
        });
    })();

    /**
     * used in path: resources\views\threads\show.blade.php
     */
    (function replySubmit() {

        $('.btnReply').on('click', function (e) {

            var $form = $('.replyPublishArea');

            var threadId = $form.find('.threadId').val();
            var textAreaContetn = $form.find('.replyBodyTextArea').val();

            if (textAreaContetn === null || textAreaContetn === '') {
                return;
            }

            axios.post('/replies', {
                threadId: threadId,
                replyBody: textAreaContetn,
            }).then((response) => {

                var respData = response.data;

                if (respData.state === true) {
                    success(respData);
                } else {
                    showFlashMessage(respData.message, 'warning');
                    console.log(respData.state + ' ' + respData.message);
                }
            }).catch((response) => {
                console.log(response);
            });

            function success(respData) {
                $('.replyBodyTextArea').val('');
                var replyId = respData.replyId;
                var replyBody = respData.replyBody;
                var replyUserId = respData.replyUserId;
                createReplyElement(replyId, replyBody, replyUserId, respData.username);
                setRepliesCount(respData.replies_count);
                showFlashMessage(respData.message);
            }
        });

        function createReplyElement(replyId, replyBody, replyUserId, username) {
            var replycomponent = `<div class="row reply">
        <div class="col-md-8" id='reply-${replyId}'>
            <div class="card">
                <div class="card-header level">
                    <div class="flex">
                        By:&nbsp;<a href="/users/${replyUserId}">${username}</a>&nbsp;&nbsp;|&nbsp;&nbsp;Just now
                      <!--  @can('delete', $reply) -->
                            <div class='deleteReplyArea inline'>
                            <input type="hidden" class='replyId' value="${replyId}">
                            &nbsp;<span class='btn-span deleteReplyBtn'>Delete</span>
                            </div>
                      <!--  @endcan -->
                      <!--  @can('update', $reply) -->
                            <!-- The editing is done through JavaScript -->
                            <div class='editReplyMode inline'>
                            <input type="hidden" class='replyId' value="${replyId}">
                            &nbsp;<span class='btn-span editReplyBtn'>Edit</span>
                            </div>
                       <!-- @endcan -->
                    </div>
                  <!--  @if(auth()->check()) -->
                    <div class='likeArea'>
                        <span class="likesCounter">0</span>
                        <input type="hidden" class='replyId' value="${replyId}">
                        <span class='btn-span likeReplyBtnToggle'>Like</span>
                    </div>
                 <!--   @endif -->
                </div>
                <div id="reply-container-${replyId}">
                    <div class="card-body" id="reply-body-${replyId}">
                        ${replyBody}
                    </div>
                </div>
            </div>
        </div>
    </div>`;

            $('.repliesArea').prepend(replycomponent);
        }
    })();

    /*
     * To show a flash message from within JavaScript based on server response
     *
     * @param string message
     * @returns void
     */
    function showFlashMessage(message, level = 'success') {
        var flashElement = `<div class="alert alert-${level} redirect-alert" role="alert">${message}</div>`;
        $('.flashDiv').append(flashElement);
        $('.redirect-alert').fadeOut(5000, function () {
            $(this).remove();
        });
    }

    /**
     * used in path: resources\views\threads\show.blade.php
     */
    $('body').on('click', '.likeArea .likeReplyBtnToggle', function () {

        $likeArea = $(this).parent();
        $id = $likeArea.find('.replyId').val();

        axios.post('/users/likereply', {
            reply_id: $id,
        }).then((response) => {
            $likeArea.find('.likesCounter').text(response.data.likesCount);
            $(this).text(response.data.was_it_like_or_unlike ? 'Unlike' : 'Like');
        }).catch((error) => {
            console.log(error);
        });
    });

    /**
     * used in path: resources\views\threads\index.blade.php
     */
    $('.deleteThreadArea').on('click', '.deleteThreadBtn', function () {

        var $deleteThreadArea = $(this).parent();
        var $threadBox = $deleteThreadArea.closest('.thread');
        $id = $deleteThreadArea.find('.threadId').val();

        axios.post('/threads/delete', {
            _method: 'delete',
            thread_id: $id,
        }).then((response) => {
            if (response.data.state === true) {
                $($threadBox).fadeOut(1000);
            } else {
                console.log('thread cannot be deleted due to server issue.');
            }
        }).catch((error) => {
            console.log(error);
        });
    });

    /**
     * used in path: resources\views\threads\show.blade.php
     */
    $('body').on('click', '.deleteReplyArea .deleteReplyBtn', function () {

        var $deleteReplyArea = $(this).parent();
        var $replyBox = $deleteReplyArea.closest('.reply');
        $id = $deleteReplyArea.find('.replyId').val();

        axios.post('/replies/delete', {
            _method: 'delete',
            reply_id: $id,
        }).then((response) => {
            if (response.data.state === true) {
                $replyBox.fadeOut(500);
                setRepliesCount(response.data.replies_count);
            } else {
                console.log('reply cannot be deleted due to server issue.');
            }
        }).catch((error) => {
            console.log(error);
        });
    });

    function setRepliesCount(replies_count) {
        $('#replies_count').text(replies_count)
        $('#replies_name').text(replies_name(replies_count))
    }

    function replies_name(count) {
        return (count === 1) ? 'reply' : 'replies';
    }

    /**
     * used in path: resources\views\threads\show.blade.php
     */
    (function replyEdit() {

        $('body').on('click', '.editReplyMode .editReplyBtn', function () {

            var $editBtn = $(this); // The button is actually a <span> element.

            // Disable the Edit button.
            $editBtn.removeClass('editReplyBtn btn-span').addClass('disable');
            var $editReplyModeArea = $editBtn.parent();
            var id = $editReplyModeArea.find('.replyId').val();
            var $replyArea = $('#reply-body-' + id);
            var oldReplyBody = $replyArea.text().replace(/^\s+|\s+$/g, '');
            var replyContainer = $('#reply-container-' + id);

            var editingReplyAreaHtml =
                    `<div>
                    <div class="form-group ma-5">
                        <textarea rows="3" name="replyBody" id="edit-reply-${id}" class="form-control">${oldReplyBody}</textarea>
                    </div>
                    <div class="row ml-10 h-44">
                        <div class='col-xs-6'>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-sm submitReplyBtn">Submit</button>
                            </div>
                        </div>
                        <div class='col-xs-6 ml-10'>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-sm btnCancelEditMode">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>`;

            replyContainer.html(editingReplyAreaHtml);

            replyContainer.off('click').on('click', '.submitReplyBtn', function () {

                var editedReply = $(`#edit-reply-${id}`).val() || ''; // if undefined then empty string will be assigned
                editedReply = editedReply.trim();

                if (editedReply === oldReplyBody || editedReply === '' || editedReply == null) {
                    endEditingMode(oldReplyBody);
                    return;
                }

                axios.post('/replies/edit', {
                    _method: 'patch',
                    replyId: id,
                    replyBody: editedReply
                }).then((response) => {

                    var respData = response.data;

                    if (respData.state === true) {
                        endEditingMode(editedReply);
                    } else {
                        showFlashMessage(respData.message, 'warning');
                        console.log('reply cannot be updated due to server issue.');
                    }
                }).catch((error) => {
                    console.log(error);
                });
            });

            replyContainer.on('click', '.btnCancelEditMode', function () {
                endEditingMode(oldReplyBody);
            });

            function endEditingMode(editedReply) {
                var replyBodySubmittedTemplate = `<div class="card-body" id="reply-body-${id}">${editedReply}</div>`;
                replyContainer.html(replyBodySubmittedTemplate);
                $editBtn.addClass('editReplyBtn btn-span').remove('disable')
            }
        });
    })();

    /**
     * used in path: resources\views\threads\show.blade.php
     */
    $('.btn_subscribe').on('click', function () {

        var threadId = $('.threadId').val();

        axios.post('/subscriptions', {
            threadId: threadId
        }).then((response) => {
            if (response.data.status === true) {
                $(this).text(response.data.was_it_subscribe_or_unsubscribe ? 'Unsubscribe from this thread' : 'Subscribe to this thread');
            } else {
                console.log('Something wrong happened at the server side');
            }
        }).catch((response) => {
            console.log(response);
        });
    });

    /**
     * used in path: resources\views\layouts\app.blade.php
     */
    $('.redirect-alert').fadeOut(5000);


    /**
     *  used in path: resources\views\threads\show.blade.php
     */
    var link = window.location.href;
    // check if this matched string exists in the current link;
    // '#reply-' followed by digit
    if (link.match(/(?<name>#reply-)\d+/) !== null) {
        // match '#reply-' followed by digit.
        var intendedReplyHash = link.match(/(?<name>#reply-)\d+/)[0];
        // get the digit from matched hash above, to be used as the id
        var replyId = intendedReplyHash.match(/\d+/)[0];
        var $replyBox = $('#reply-body-' + replyId);
        // do the background-color animated change.
        $replyBox.css({"background-color": "#f4a83d", "transition": "background-color 1.8s ease"});
        setTimeout(function () {
            $replyBox.css("background-color", "#ffffff");
        }, 2000);
    }

});