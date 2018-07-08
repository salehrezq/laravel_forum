$(function () {

    /**
     * used in path: resources\views\threads\show.blade.php
     */
    $('.likeArea').on('click', '.likeReplyBtnToggle', function () {

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
    $('.deleteReplyArea').on('click', '.deleteReplyBtn', function () {

        var $deleteReplyArea = $(this).parent();
        var $replyBox = $deleteReplyArea.closest('.reply');
        $id = $deleteReplyArea.find('.replyId').val();

        axios.post('/replies/delete', {
            _method: 'delete',
            reply_id: $id,
        }).then((response) => {
            if (response.data.state === true) {
                $replyBox.fadeOut(500);
                $('#replies_count').text(response.data.replies_count)
                $('#replies_name').text(replies_name(response.data.replies_count))
            } else {
                console.log('reply cannot be deleted due to server issue.');
            }
        }).catch((error) => {
            console.log(error);
        });
    });

    function replies_name(count) {
        return (count === 1) ? 'reply' : 'replies'
    }

    /**
     * used in path: resources\views\threads\show.blade.php
     */
    $('.editReplyMode').on('click', '.editReplyBtn', function () {

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