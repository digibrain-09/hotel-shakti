/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */

"use strict";

$(".daterange-cus").daterangepicker(
    {
        locale: { format: "YYYY-MM-DD" },
        drops: "down",
        autoUpdateInput: false,
        opens: "right",
    },
    function (start, end) {
        $('.daterange-cus').val(start.format("YYYY-MM-DD") + " to " +end.format("YYYY-MM-DD"))
        $("input[name=startdate]").val(start.format("YYYY-MM-DD"));
        $("input[name=enddate]").val(end.format("YYYY-MM-DD"));
        $("form#filter_form").trigger("submit");
    }
);

$(".delete-movement-modal").on("click", function () {
    if ($(".movement-secrets:checked").length > 0) {
        var html = "";
        $(".movement-input").remove();
        $(".movement-secrets:checked").each(function () {
            var secret = $(this).val();
            html +=
                '<input type="hidden" name="movement[]" class="movement-input" value="' +
                secret +
                '" />';
        });
        $("form#delete_all").find('input[name="start_date"]').val(0)
        $("form#delete_all").find('input[name="end_date"]').val(0)
        $('#selected-text').show();
        $('#interval-text').hide();
        $("form#delete_all").append(html);
        $("#delete-movements").modal({ backdrop: "static", keyboard: false });
    } else if($("input[name=startdate]").val()!= "" && $("input[name=enddate]").val()!="" ) {
        $(".movement-input").remove();
        $('#interval-text').show();
        $('#selected-text').hide();
        $("form#delete_all").find('input[name="start_date"]').val($("input[name=startdate]").val());
        $("form#delete_all").find('input[name="end_date"]').val($("input[name=enddate]").val());        
        $("#delete-movements").modal({ backdrop: "static", keyboard: false });

    } else {
        $("#toast-danger")
            .find(".toast-body")
            .html($('#error_message').html());
        $("#toast-danger").toast({ animation: true, delay: 1500 });
        $("#toast-danger").toast("show");
    }
});
$(document).on("click", "#select-checkbox", function () {
    if ($(this).prop("checked") == true) {
        $(".movement-secrets").prop("checked", true);
    } else {
        $(".movement-secrets").prop("checked", false);
    }
});
$(document).on("submit", "#delete_all", function () {
    var html = "";
    $(".movement-input").remove();
    $(".movement-secrets:checked").each(function () {
        var secret = $(this).val();
        html +=
            '<input type="hidden" name="movement[]" class="movement-input" value="' +
            secret +
            '" />';
    });
    $("form#delete_all").append(html);
});
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$("#import-movement-btn").on("click",  function (e) {
    e.preventDefault();
    $('#import-movements').modal({backdrop:'static',keyboard:false});
});
var ImportProgress;
$("#import-form").on("submit",  function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        url: $(this).attr('action'),
        type: "POST",
        data: formData,
        success: function (res) {
            if(res.status =='success'  && res.msg){
                $('#import-movements').modal('hide');
                if(res.msg.alert_success){
                    alertMessage('success',res.msg.alert_success);
                }
                if(res.msg.alert_warning){
                    alertMessage('warning',res.msg.alert_warning);
                }
                setTimeout(function(){
                    window.location.reload();
                },2000);
            }else{
                window.location.reload();
            }
            
        },
        cache: false,
        contentType: false,
        processData: false
    });
    ImportProgress = setInterval(function(){
        checkProgress();
    },2000)
});
function checkProgress(){
    $.post(checkImport,{},function(res){
        console.log(res);
        if(res.percent!=0){
            $('.progress-bar').parent().removeClass('d-none');
            $('.progress-bar').css({width:res.text});
            $('.progress-bar').attr('aria-valuenow',res.percent);
            $('.progress-bar').text(res.text);
            if(res.percent == 100){
                clearInterval(ImportProgress);
            }
        }
    });
    if($('.progress-bar').attr('aria-valuenow')==100){
        clearInterval(ImportProgress)
    }
}