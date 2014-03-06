// getActiveTickets counts the number of tickets on the page, then checks the server
// to see if they match. If they do, nothing happens, if they don't, the page 
// reloads.
function getActiveTickets() {
    $.post("<?php echo sanitizeHTML(BASE_URL);?>/tickets/ticketAJAX.php",{
        type: "get_active_ticket_count"
    } , function (data) {
        var tickets = $("#recent-posts").children('h4').size();
        if (data != tickets) {
            location.reload();
        }
    })
    setTimeout(arguments.callee, 30000);
}
// getTicketInfo grabs some of the ticket info from the server in JSON format
// and updates the ticket. This function is called whenever certain things are
// clicked.
function getTicketInfo(ticket_id) {
    $("input[type=radio],input[type=checkbox]").prop('checked',false);
    $("#appt").hide();
    $("#apptDate").val('');
    $("#apptTime").val('');
    $.getJSON(
        "<?phpecho sanitizeHTML(BASE_URL);?>/tickets/ticketAJAX.php?ticket_id="
        +ticket_id+"&type=get_ticket",
        function(json){
            var i=0;
            var edit_priority_tag = '#edit-priority-'+json.priority;
            var edit_addon_tag = '#edit-addon-'+json.addon;
            var category_length = json.categories.length;
            while (i < category_length) {
                $("#cat_"+json.categories[i].category_id).prop('checked',true);
                i++;
            }
            $( "#edit-ticket-textarea").html(json.description);
            $(edit_priority_tag).prop('checked',true);
            $(edit_addon_tag).prop('checked',true);
            if (json.addon == "Appointment") {
                $("#appt").show('bounce',1000);
                var appt_array = json.appt.split(" ");
                $("#apptDate").val(appt_array[0]);
                $("#apptTime").val(appt_array[1]);
                $("#appt"+appt_array[2]).prop('checked', true);
            } else {
                $("#apptPM").prop('checked', true);
            }
            $(".radio").buttonset("refresh");
        })
}
// getIssueInfo is called to update the ticket issuer info when someone clicks the
// ticket. The information is in JSON format.
function getIssuerInfo(ticket_id) {
    $.getJSON(
        "<?php echo sanitizeHTML(BASE_URL);?>/tickets/ticketAJAX.php?ticket_id="
        +ticket_id+"&type=get_ticket",
        function(json){
            if (json.issuer_first_name != null && json.issuer_last_name != null) {
                var full_name = json.issuer_first_name+' '+json.issuer_last_name;
            } else {
                var full_name = null;
            }
            var email = '<a href="mailto:'+json.email+'">'+json.email+'</a>';
            $("#edit-issuer-ticket-id").val(json.id);
            $("#issuer-gatorlink-"+ticket_id).html(json.issuer_gatorlink);
            $("#issuer-name-"+ticket_id).html(json.full_name);
            $("#issuer-email-"+ticket_id).html(email);
            $("#issuer-phone-"+ticket_id).html(json.phone);
            $("#edit-issuer-gatorlink").val(json.issuer_gatorlink);
            $("#edit-issuer-name").val(full_name);
            $("#edit-issuer-email").val(json.issuer_email);
            $("#edit-issuer-phone").val(json.issuer_phone);
        })
}
// This little function calls getActiveTickets every 30 seconds to make sure the
// ticket page is up to date.
setTimeout(getActiveTickets,30000);
