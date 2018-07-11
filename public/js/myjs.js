$(function () {

    /**
     * used in path: resources\views\threads\show.blade.php
     */
    $('.reply_form').on('submit', function (e) {

        e.preventDefault();
        var $form = $(this);
        var formData = $form.serializeArray();

        var threadId = formData[1].value;
        var textAreaContetn = formData[2].value;

        if (textAreaContetn === '' || textAreaContetn == null) {
            return;
        }

        axios.post('/replies', {
            threadId: threadId,
            replyBody: textAreaContetn,
        }).then((response) => {
            if (response.data.state === true) {
                success(response)
            } else {
                console.log(response.data.state + ' ' + response.data.message);
            }
        }).catch((response) => {
            console.log(response);
        });

        function success(response) {
            $('.replyBodyTextArea').val('');
            var replyId = response.data.replyId;
            var replyBody = response.data.replyBody;
            var replyUserId = response.data.replyUserId;
            createReplyElement(replyId, replyBody, replyUserId, response.data.username);
            setRepliesCount(response.data.replies_count);
            showFlashMessage(response.data.message);
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

    /*
     * To show a flash message from within JavaScript based on server response
     * 
     * @param string message
     * @returns void
     */
    function showFlashMessage(message) {
        var flashElement = `<div class="alert alert-success redirect-alert" role="alert">${message}</div>`;
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

        replyContainer.on('click', '.submitReplyBtn', function () {

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
                if (response.data.state === true) {
                    endEditingMode(editedReply);
                } else {
                    endEditingMode(oldReplyBody);
                    console.log('reply cannot be updated due to server issue.');
                }
            }).catch((error) => {
                console.log(error);
            });
        });

        replyContainer.on('click', '.btnCancelEditMode', function () {
            endEditingMode(oldReplyBody)
        });

        function endEditingMode(editedReply) {
            var replyBodySubmittedTemplate = `<div class="card-body" id="reply-body-${id}">${editedReply}</div>`;
            replyContainer.html(replyBodySubmittedTemplate);
            $editBtn.addClass('editReplyBtn btn-span').remove('disable')
        }
    });

    /**
     * used in path: resources\views\threads\show.blade.php
     */
    $('.btn_subscribe').on('click', function () {

        var threadId = $('.threadId').val();

        axios.post('/subscriptions', {
            threadId: threadId
        }).then((response) => {
            if(response.data.status === true){
              $(this).text(response.data.was_it_subscribe_or_unsubscribe ? 'Unsubscribe from this thread' : 'Subscribe to this thread');
            }else{
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