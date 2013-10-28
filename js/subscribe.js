//User subscription javascript
$(document).ready(function(){
    function sub5() {
        if ($('#gift').is(':checked')){
            var cc=$('#country2').val();
        }
        else {
            var cc=$('#country').val();
        }
        //if (!(cc==13 || cc==153)){
        if (cc!=153){
            $('#sub2').attr('disabled','disabled');
            $('#sub5').attr('disabled','disabled');
            $('#sub6').attr('disabled','disabled');
            $('#sub7').attr('disabled','disabled');
            var sub=$('input:radio[name=package]:checked').val();
            if (sub==5)
                $('input:radio[name=package]').val(["1"]);
            if (sub==6)
                $('input:radio[name=package]').val(["1"]);
            if (sub==7)
                $('input:radio[name=package]').val(["1"]);
        }
        else{
            $('#sub2').removeAttr('disabled');
            $('#sub5').removeAttr('disabled');
            $('#sub6').removeAttr('disabled');
            $('#sub7').removeAttr('disabled');
	}

    };

    $('#subscribeform').bind("reset", function() {
        $('#gift').attr('checked', false).trigger('change');
        $('#renewal').attr('checked', false).removeAttr('disabled').trigger('change');
        $('#sub5').removeAttr('disabled');
        $('#sub6').removeAttr('disabled');
        $('#sub7').removeAttr('disabled');
        $('#cid').val('');
        $('#search_text').val('');
        $('#renewal_pw').removeAttr('disabled');
        $('#lookup').removeAttr('disabled');
        $('#search').removeAttr('disabled');
        $('#search_text').removeAttr('disabled');
        setTimeout(function() {
            $('#country').trigger('change');
            $('#country2').trigger('change');
        },10);
    });

    $('#renewal').change(function(){
        var thisCheck = $(this);
        if (thisCheck.is(':checked')){
            $('#renewal_bit').fadeIn();
            $('#renewal_pw').removeAttr('disabled').focus();
            $('#lookup').removeAttr('disabled');
            $('#search').removeAttr('disabled');
            $('#search_text').removeAttr('disabled');
        }
        else{
            $('#renewal_bit').fadeOut();
            $('#renewal_pw').attr('disabled','disabled');
            $('#lookup').attr('disabled','disabled');
            $('#search').attr('disabled','disabled');
            $('#search_text').attr('disabled','disabled');
        }
    });

    $('#gift').change(function(){
        var thisCheck = $(this);
        if (thisCheck.is(':checked')){
            $('#gift_bit').fadeIn();
        }
        else{
            $('#gift_bit').fadeOut();
        }
        sub5();
    });

    $('#search').click(function(e){
        e.preventDefault();
        var sstring=$('#search_text').val()
        if (sstring != ''){
            $.post("/ajax/lookup.php", { "search": sstring }, function(data){
                if (data.status==0){
                    alert(data.message);
                    return false;
                }
                $('#renewal_pw').val(data.rid);
                $('#lookup').trigger('click');
            }, "json");
        }
    });
    $('#lookup').click(function(e){
        var cid =$('#renewal_pw').val();
        if (cid == ''){
            alert ('Please enter your subscriber ID');
            $('#renewal_pw').focus();
            return false;
        }
        $.post("/ajax/lookup.php", { "cid": cid }, function(data){
            if (data.status==0){
                alert(data.message);
                $('#renewal_pw').focus();
                return false;
            }
// Only allow one lookup
            $('#cid').val(cid);
            $('#renewal').attr('disabled','disabled');
            $('#renewal_pw').attr('disabled','disabled');
            $('#lookup').attr('disabled','disabled').blur();
            $('#search').attr('disabled','disabled');
            $('#search_text').attr('disabled','disabled');

            for (var i in data) {
                $('#'+i).val(data[i]);
            }
            $('#country').trigger('change');
            if (data.gift){
                $('#gift').attr('checked', true);
                $('#gift').trigger('change');
                $('#country2').trigger('change');
            }
        }, "json");
        return false;
    });

    $('#country').change(function(){
        var cc=$(this).val();
        if (cc!=153){
            $('#region').attr('disabled','disabled');
            $('#region_row').fadeOut();
            $('#regiono').removeAttr('disabled');
            $('#region_other_row').fadeIn();
        }
        else{
            $('#region').removeAttr('disabled');
            $('#region_row').fadeIn();
            $('#regiono').attr('disabled','disabled');
            $('#region_other_row').fadeOut();
        }
        sub5();
    });

    $('#country2').change(function(){
        var cc=$(this).val();
        if (cc!=153){
            $('#region2').attr('disabled','disabled');
            $('#region_row2').fadeOut();
            $('#regiono2').removeAttr('disabled');
            $('#region_other_row2').fadeIn();
        }
        else{
            $('#region2').removeAttr('disabled');
            $('#region_row2').fadeIn();
            $('#regiono2').attr('disabled','disabled');
            $('#region_other_row2').fadeOut();
        }
        sub5();
    });

    $('#subscribeform').submit(function(e){
        var f1= new Array('forename','surname','email','phone','address1','city','country','pcode');
        var f2= new Array('name2','email2','phone2','address12','city2','country2','pcode2');
        for (var i in f1){
            var val=$('#'+f1[i]).val();
            if ((val=='') || (val=='0') || (val=='Please select')){
                alert('Please enter '+f1[i]);
                $('#'+f1[i]).focus();
                e.preventDefault();
                return false;
            }
            if ($('#country').val()==153 && $('#region').val()=='Please select'){
                alert('Please select region');
                $('#region').focus();
                e.preventDefault();
                return false;
            }
        }
        if ($('#gift').attr('checked')==true){
            for (var i in f2){
                var val=$('#'+f2[i]).val();
                if ((val=='') || (val=='0') || (val=='Please select')){
                    alert('Please enter '+f2[i]);
                    $('#'+f2[i]).focus();
                    e.preventDefault();
                    return false;
                }
            }
            if ($('#country2').val()==153 && $('#region2').val()=='Please select'){
                alert('Please select region');
                $('#region2').focus();
                e.preventDefault();
                return false;
            }
        }
        return true;
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

    $('#country').trigger('change');
    $('#country2').trigger('change');

    if ($('#renewal_pw').val() == ''){
        if (window.confirm('Is this a renewal?')){
            $('#renewal').attr('checked', true);
            $('#renewal').trigger('change');
        }else{
		 $('#title').focus();
	}
    }
    else { // only set with GET
        $('#renewal').attr('checked', true);
        $('#renewal').trigger('change');
        $('#lookup').trigger('click');
    }
});
