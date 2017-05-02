<?php
    global $ts;
?>
<style>
    input.btn-grey {background: #999999;color: white !important;}

input#send_offer {
    padding-left: 10px;
}

.w-video-detail {
    width: 100% !important;
    padding-bottom: 10px !important;
    margin-bottom: 10px;
}


.w-bottom-btn {
    padding: 0 !important;
}
.packageCls {
    font-size: 11px !important;
    color: #999999 !important;
    padding : 0px 7px !important;
}
.ui-autocomplete-input {
    font-size: 11px !important;
    color: #999999 !important;
    padding-left: 10px !important;
}
    
    .w-row_h2 {
    color: white !Important;
    font-size: 18px;
}

.timez {
    margin-bottom: 10px;
}

.timez span {
    margin-left: 10px;
}


.timez_cross {
    font-size: 22px;
    line-height: 0;
    position: relative;
    top: 3px;
}

.timez_cross a {
    color: inherit;
    text-decoration: none;
}
</style>
<?php if(isset($error)){?>
    <div class="aj-alert">
        <div class="alert alert-danger" role="alert"><?php echo $error;?></div>
    </div>
<?php }elseif(isset($success)){?>
    <div class="aj-alert">
        <div class="alert alert-success" role="alert"><?php echo $success;?></div>
    </div>
<?php }?>
<div class="col-md-12 w-video-section" xmlns="http://www.w3.org/1999/html">
<!--    <form method="post" id="post-packages" name="post-packages">-->
        <div class="col-md-6 ">
            <div class="w-video-detail">
            <div class="w-date-block new-date-block">
                
                <form method="post" id="schedule-meeting-form" name="schedule_meeting">
                    
                    <div class="w-row w-clearfix">
                        <select name="packages_deal" id="packages_deal" class="validate[required] form-control packageCls" data-errormessage-value-missing="Packages Deal">
                            <option value="">Package Deal</option>
                            <option value="10">10 hours - 2451 kr (5% discount)</option>
                            <option value="20">20 hours - 4644 kr (10% discount)</option>
                            <option value="40">40 hours - 8772 kr (15% discount)</option>
                        </select>
                    </div>
                    
                    <div class="w-row w-clearfix" id="clacHours">
                        <h2 class="w-row_h2">You have booked <span id="clacTime">0</span> of <span id="totalTime">0</span> hours</h2>    
                    </div>
                    
                    <div class="w-row w-clearfix" id="selectedRow">
                        
                    </div>
                    
                    <div class="w-row w-clearfix">
                        <div class="w-datepicker">
                            <input type="text" name="date_time" id="date_time_1" value="" placeholder="Velg tidspunkt" readonly class="validate[required] form-control" data-errormessage-value-missing="Vennligst fyll ut" />
                            <span class="caret"></span>
                        </div>

                        <script type="text/javascript">
                            jQuery(function () {
                                //jQuery('#datetimepicker4').datetimepicker({sideBySide: true});
                                jQuery('#date_time_1').datetimepicker({format: 'YYYY-MM-DD HH:mm', sideBySide: true, useCurrent: false, stepping: 30, ignoreReadonly: true, minDate: '<?php echo date('Y-m-d');?>', enabledHours: [6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22]});
                            });
                        </script>

                        <div class="w-duration">
                            <select name="duration" id="duration" class="validate[required] form-control" data-errormessage-value-missing="Vennligst fyll ut">
                                <option value="30">30 minutes</option>
                                <option value="1">1 hour</option>
                                <option value="1.5">1.5 hour</option>
                                <option value="2">2 hours</option>
                                <option value="3">3 hours</option>
                            </select>
                        </div>
                    </div>
                    <div class="w-row w-clearfix">
                        <input type="submit" class="btn btn-grey" name="btn_schedule_meeting" id="btn_schedule_meeting" value="Schedule Meeting" />
                    </div>
                
                <div class="w-checkboxes-holder">
                    <span>* individual meeting times can be changed no later than two days before the agreed meeting time.</span>
                </div>

                <div class="w-row  w-clearfix  mbn">
                    <input type="text" name="email_address" id="email_address_of_student" class="validate[required] form-control ui-autocomplete-input"  value="" placeholder="E-Mail address of student" />
                </div>

                </form>
            </div>
        </div>
            
            <div class="w-row w-clearfix">
                <div class="w-bottom-btn">
                    <input type="button" class="btn btn-success" name="send_offer_teacher" id="send_offer_teacher" value="Send offer" />
                </div>
            </div>
        </div>
        
<!--    </form>-->
</div>