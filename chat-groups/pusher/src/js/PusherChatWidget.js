/**
 * Creates an instance of a PusherChatWidget, binds to a chat channel on the pusher instance and
 * and creates the UI for the chat widget.
 *
 * @param {Pusher} pusher The Pusher object used for the chat widget.
 * @param {Map} options A hash of key value options for the widget.
 */
function PusherChatWidget(pusher, options) {
    PusherChatWidget.instances.push(this);
    var self = this;

    this._pusher = pusher;
    this._autoScroll = true;

    options = options || {};
    this.settings = $.extend({
        maxItems: 50, // max items to show in the UI. Items beyond this limit will be removed as new ones come in.
        chatEndPoint: 'php/chat.php', // the end point where chat messages should be sanitized and then triggered
        channelName: document.location.href, // the name of the channel the chat will take place on
        appendTo: document.body, // A jQuery selector or object. Defines where the element should be appended to
        debug: true
    }, options);

    if (this.settings.debug && !Pusher.log) {
//    Pusher.log = function(msg) {
//      if(console && console.log) {
//        console.log(msg);
//      }
//    }
    }

    // remove any unsupported characters from the chat channel name
    // see: http://pusher.com/docs/client_api_guide/client_channels#naming-channels
    this.settings.channelName = PusherChatWidget.getValidChannelName(this.settings.channelName);

    this._chatChannel = this._pusher.subscribe(this.settings.channelName);

    this._chatChannel.bind('chat_message', function(data) {
        self._chatMessageReceived(data);
    })

    this._chatChannel.bind('user_list', function(data) {
        self._user_list_response(data);
    })

    
    this._nicknameEl = $("#admin_nickname").val();
    this._messageInputEl = $("#msg");

    $("#send_btn").click(function() {
        self._sendChatButtonClicked();
    })
    $('#msg').keyup(function(e) {
            if (e.keyCode == 13)
            {
                self._sendChatButtonClicked();
            }
    });
    $("#reg_user_for_chat").click(function() {
        self._registerUserClick();
    });
    $("#unreg_user_for_chat").click(function() {
        self._unregisterUserClick();
    });
   
}
;
PusherChatWidget.instances = [];

/* @private */
PusherChatWidget.prototype._user_list_response = function(data) {
    
    //console.log(data);
    var nicknames = data.nicknames;
    var rsp_html = "";
    
    
    
    $.each(nicknames, function( index, value ) {
        rsp_html += '<span>'+value+'</span>';
    });
    
    
    $("#user_list").html(rsp_html);
    


};
/* @private */
PusherChatWidget.prototype._registerUserClick = function() {
    var self = this;
    $("#reg_user_for_chat_rsp").html("processing...");
    var nickname = $("#nickname_reg").val();
    var data = {
        nickname: $.trim(nickname),
        uid: $.trim($("#uid").val()),
        gid: $.trim($("#gid").val())
    };
    if (nickname !== "") {
    $.ajax({
        url: this.settings.chatEndPoint,
        type: 'post',
        data: {
            'user_list': data
        },
        dataType: "json",
        complete: function(xhr, status) {
            //Pusher.log('Chat message sent. Result: ' + status + ' : ' + xhr.responseText);
            
        },
        success: function(result) {
            
            var rsp = result.activity.reg_rsp;
            if (rsp.type === "error")
            {
                $("#reg_user_for_chat_rsp").html(rsp.msg);
            }
            else {
                $(".register_div").html(rsp.msg);
                //$("#user_list").append('<span>' + nickname + '</span>');
                location.reload();
            }

        }
    });
    $("#reg_user_for_chat_rsp").html("");
    }
    else {
        $("#reg_user_for_chat_rsp").html("<span style='color:red;' >Your nickname is empty</span>");
    }



            

};

/* @private */
PusherChatWidget.prototype._unregisterUserClick = function() {
    var self = this;
    
    var data = {
        uid: $.trim($("#uid").val()),
        gid: $.trim($("#gid").val())
    };
    
    $.ajax({
        url: this.settings.chatEndPoint,
        type: 'post',
        data: {
            'user_list_remove': data
        },
        dataType: "json",
        complete: function(xhr, status) {
            //Pusher.log('Chat message sent. Result: ' + status + ' : ' + xhr.responseText);
        },
        success: function(result) {
            
            var rsp = result.activity.reg_rsp;
            if (rsp.type === "ok")
            {
                c("#rsp").addClass("success");
                c("#rsp").html(rsp.msg);
                location.reload();
            }
            else {
                c("#rsp").addClass("error");
                c("#rsp").html(rsp.msg);
            }
        }
    });



            

};


/* @private */
PusherChatWidget.prototype._chatMessageReceived = function(data) {
    //console.log("message receved");
    //console.log(data);
    //console.log(PLUGIN_PATH);


    var rsp_html = "";
    
    var time = data.msg_time;
    var msg = data.msg;
    var name = data.msg_name;


    

    rsp_html = '<div class="chat_msg append"><span class="time">' + time + '</span><span class="userName" >' + name + '</span>  <span class="message">' + msg + '</span> </div> ';
    $("#chatbox").append(rsp_html);
    var totalHeight = $('#chatbox')[0].scrollHeight;
    $("#chatbox").scrollTop(totalHeight);

    $("#previewArea").html("");
    
    MathJax.Hub.Queue(["Typeset", MathJax.Hub]);


};



/* @private */
PusherChatWidget.prototype._sendChatButtonClicked = function() {
    
    if ($("#uid").val() == 0) {
        var rsp_msg = 'Meld deg p&aring; for &aring; delta i gruppediskusjonen';
        $("#rsp").html(rsp_msg);
        $("#rsp").addClass("error");
        $('html, body').animate({scrollTop:$('#rsp').position().top}, 'fast');
        return false;
    }
            
    if($("#uid").val() != 0 && $("#chat_disable_ck").val() == 1){
        var rsp_msg = 'Det er ikke mulig &aring; publisere kommentarer f&oslash;r gruppens starttid';
        $("#rsp").html(rsp_msg);
        $("#rsp").addClass("error");
        $('html, body').animate({scrollTop:$('#rsp').position().top}, 'fast');
        return false;
    }
    
    var msg = $.trim($("#msg").val());

    if (!msg) {
        return;
    }
    var gid = $("#gid").val();
    var uid = $("#uid").val();
    var chat_html;

    var d = new Date();
    var h = d.getUTCHours();
    var m = d.getUTCMinutes();
    //it is pm if hours from 12 onwards
    var time_suffex = (h >= 12) ? 'PM' : 'AM';
    //only -12 from hours if it is greater than 12 (if not back at mid night)
    h = (h > 12) ? h - 12 : h;
    //if 00 then it is 12 am
    h = (h == '00') ? 12 : h;
    m = (m < 10) ? '0' + m : m;
    var msg_time = h + ":" + m + " " + time_suffex;
    var msg_name = $("#admin_nickname").val();

    var chatInfo = {
        gid: gid,
        msg: msg,
        uid: uid,
        msg_time: msg_time,
        msg_name: msg_name
    };


    this._sendChatMessage(chatInfo);
};

/* @private */
PusherChatWidget.prototype._sendChatMessage = function(data) {
    var self = this;

    $('#msg').prop("readonly",true);
    $.ajax({
        url: this.settings.chatEndPoint,
        type: 'post',
        data: {
            'chat_info': data
        },
        dataType: "json",
        complete: function(xhr, status) {
            //Pusher.log('Chat message sent. Result: ' + status + ' : ' + xhr.responseText);
            if (xhr.status === 200) {
                $('#msg').val('');
                $('#msg').focus();
            }
            $('#msg').prop("readonly",false);
        },
        success: function(result) {
            //console.log(result);

            var data_act = result.activity;
            
            
//            $.ajax({
//                type: "POST",
//                url: PLUGIN_PATH + "insert_chat.php",
//                data: {
//                    uid: data_act.uid,
//                    gid: data_act.gid,
//                    msg: data_act.msg
//                },
//                dataType: "json"
//            }).success(function(rsp) {
//                if (rsp.type === "error")
//                {
//                    console.log("error inserting msg try again");
//                }
//                else {
//                    
//                }
//            });





        }
    })
};



/**
 * converts a string into something which can be used as a valid channel name in Pusher.
 * @param {String} from The string to be converted.
 *
 * @see http://pusher.com/docs/client_api_guide/client_channels#naming-channels
 */
PusherChatWidget.getValidChannelName = function(from) {
    var pattern = /(\W)+/g;
    return from.replace(pattern, '-');
}
