// admin subscriber maintenance functions
$(document).ready(function(){
    $('#subscribeform').bind("reset", function() {
        $('#gift').attr('checked', false).trigger('change');
        $('#renewal').val('N');
        $('#sub3').attr('disabled','');
        setTimeout(function() {
            $('#country').trigger('change');
            $('#country2').trigger('change');
        },10);

    });

    $('#city').change(function(){
        var city=$('#city').val();
        if ($('#country').val() == 153){
            $.ajax({ "type": 'POST',
                "url": "/ajax/region.php",
                "data": {"city":city},
                "success": function(data){
                    if (data.status==1){
                        $('#region').val(data.region);
                        $('#city').val(data.city);
                    }
                },
                "dataType": "json",
                "async": false});
        }
    });

    $('#city2').change(function(){
        var city=$('#city2').val();
        if ($('#country2').val() == 153){
            $.ajax({ "type": 'POST',
                "url": "/ajax/region.php",
                "data": {"city":city},
                "success": function(data){
                    if (data.status==1){
                        $('#region2').val(data.region);
                        $('#city2').val(data.city);
                    }
                },
                "dataType": "json",
                "async": false});
        }
    });

    $('#page').change(function(){
        $('#Scanform').submit();
    });

    $('#Clear').click(function(){
        $('#Search').val('');
    });

    $('#gift').change(function(){
        var thisCheck = $(this);
        if (thisCheck.is(':checked')){
            $('#gift_bit').fadeIn();
        }
        else{
            $('#gift_bit').fadeOut();
        }
    });

    $('#country').change(function(){
        var cc=$(this).val();
        if (cc!=153){
            $('#region').attr('disabled','disabled');
            $('#region_row').fadeOut();
            $('#regiono').attr('disabled','');
            $('#region_other_row').fadeIn();
        }
        else{
            $('#region').attr('disabled','');
            $('#region_row').fadeIn();
            $('#regiono').attr('disabled','disabled');
            $('#region_other_row').fadeOut();
        }
    });

    $('#country2').change(function(){
        var cc=$(this).val();
        if (cc!=153){
            $('#region2').attr('disabled','disabled');
            $('#region_row2').fadeOut();
            $('#regiono2').attr('disabled','');
            $('#region_other_row2').fadeIn();
        }
        else{
            $('#region2').attr('disabled','');
            $('#region_row2').fadeIn();
            $('#regiono2').attr('disabled','disabled');
            $('#region_other_row2').fadeOut();
        }
    });

// These next three ensure the first,last and years all agree for new subs
    $('input:radio[name=package]').change(function(){
        var id=$('#id').val();
        if (id==0){
            var year=$('#'+this.id).val();
            var first=$('#issue').val();
            do_dates(year,first);
        }
    });
    $('#issue').change(function(){
        var id=$('#id').val();
        if (id==0){
            var year=$('input:radio[name=package]:checked').val();
            var first=$('#issue').val();
            do_dates(year,first);
        }
    });
    $('#last_issue').change(function(){
        var id=$('#id').val();
        if (id==0){
            var year=$('input:radio[name=package]:checked').val();
            var last=$('#last_issue').val();
            $.post("/ajax/lastissue.php", {"year":year,"last":last}, function(data){
                if (data.status==0){
                    alert(data.message);
                    return false;
                }
                $('#issue').val(data.first);
            }, "json");
        }
    });
    function do_dates(year,first){
        $.ajax({ "type": 'POST',
            "url": "/ajax/lastissue.php",
            "data": {"year":year,"first":first},
            "success": function(data){ if (data.status==0){
                                alert(data.message);
                                return false;
                        }
                        $('#last_issue').val(data.last)},
            "dataType": "json",
            "async": false});
    }
    $('#renew_btn').click(function(e){
        e.preventDefault();
        $('#renewal').val('Y');
        var first=$('#current').val();
        $('#issue').val(first);
        var year=$('input:radio[name=package]:checked').val();
        do_dates(year,first);
        var y=first.substr(0,4);
        var m=first.substr(4,2);
        var txt='First issue is now '+y+'-'+m;
        var last=$('#last_issue').val();
        y=last.substr(0,4);
        m=last.substr(4,2);
        txt=txt+"\nLast issue is now "+y+'-'+m;
        alert(txt);
    });

    $('.Delete').click(function(){
        if (!window.confirm('Delete this user?'))
            return false;
        return true;
    });

    $('.sortby').click(function(){
        var sortby=this.id.substr(7);
        $('#Scanform').append('<input type=hidden name=Sort value='+sortby+'>').submit();
    });

    $('img.arrow').click(function(){
        var dir=this.id.substr(4);
        $('#Scanform').append('<input type=hidden name=Dir value='+dir+'>').submit();
    });

    $('#Update').click(function(e){
        if ($('#renewal').val()=='Y'){
            if ($('input:radio[name=method]:checked').val()=='P'){
                e.preventDefault();
                alert('Please select the payment method');
                return false;
            }
        }
    });

// initial setup.
    $('#country').trigger('change');
    $('#country2').trigger('change');
    $('#gift').trigger('change');
    if ($('#last_issue').val()  > $('#current').val())
        $('#renew_btn').attr('disabled','disabled');
});
