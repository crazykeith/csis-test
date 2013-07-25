$( "#upload" ).dialog({
        autoOpen: false,
        resizable: false,
        height:"auto",
        modal: true,
        show: "scale",
        hide: "scale",
        buttons: {
            Cancel: function() {
                $('#checkout-id').val('');
                $('#file').val('');
                $("#upload-errors").text('').hide();
                $( this ).dialog( "close" );
            },
            "Upload": function() {
                var id = $('#checkout-id').val();
                var form_data = new FormData(document.getElementById('upload-form'));
                var upload_query = "<?php
                    echo sanitizeHTML(BASE_URL
                        . '/inventory/inventoryAJAX.php?action=upload');?>";
                $.ajax({
                    url: upload_query,
                    data: form_data,
                    cache: false,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    success: function (data) {
                        if (data == false || data == null) {
                            $("#upload-errors").text(
                                '* A Problem has occured,'
                                +'please refresh the page to continue.'
                            ).show();
                        } else if (data === "Your file has been uploaded to the server."){
                            $('#file').val('');
                            $("#upload-errors").text('').hide();
                            $("#upload").dialog( "close" );
                            $('#CheckoutTableContainer').jtable('reload');
                        } else {
                            $("#upload-errors").text(data).show();
                        }
                    }
                })
            }
        }
    });
    $("#CheckoutTableContainer").on("click",".upload-pdf",function(event) {
        event.preventDefault();
        var id = $(this).attr('id');
        $('#checkout-id').val(id);
        $( "#upload").dialog('open');
    });
