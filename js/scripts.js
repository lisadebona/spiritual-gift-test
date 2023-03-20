var params={};location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(s,k,v){params[k]=v});

jQuery(document).ready(function ($) {

	/* Remove all answers once completed */
	if( typeof params.completed!=undefined && params.completed==1 ) {
		if(totalQuestions>0) {
			for(i=1; i<=totalQuestions; i++) {
				Cookies.remove('SGQuestionIndex_'+i);
			}
		}
		Cookies.remove('SGCurrentPage');
		history.pushState("",document.title,siteURL);
	}


	/* Member Form */
	$("#memberform").on("submit",function(e){
		e.preventDefault();
		var form = $(this);
    var formAction = form.attr('action');

		$.ajax({
			type: "POST",
			url: formAction,
			data: form.serialize(),
			dataType: "JSON",
			beforeSend:function(data){
				$("#loader").show();
			},
			success: function(data) {
				
				var errors = data.errors;
				if(data.ok) {
					//window.location.href = data.redirect;
				} else {
					if(errors.length>0) {
						var message = '<div class="error-message">Fill in the required field(s).</div>';
						$("#form-response").html(message);
						$(errors).each(function(k,v){
							$('input[name="'+v+'"]').addClass("error");
						});
					}
				}
				$("#loader").hide();
				
			},
			complete:function(data){
				var d = data.responseJSON;
				if(d.ok) {
					window.location.href = d.redirect;
				}
			},
			error: function (xhr, desc, err) {
				//$("#loader").hide();
				if(xhr.responseText) {
					$("body").append('<div style="color:red;">'+xhr.responseText+'</div>');
				}
      }
		});

	});

	var errorMessage = '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> You must complete all questions.</div>';
	
	if( $("input#phone").length > 0 ) {
		var cleave = new Cleave('input#phone', {
		    phone: true,
		    delimiter: '-',
		    phoneRegionCode: 'US'
		});
	}

	$(document).on("click",".choice-input",function(e){
		var parent = $(this).parents(".question-item");
		var index = parent.attr("data-index");
		var parentPage = $(this).parents(".questions-group");
		var parentPageID = parentPage.attr("id");
		if( this.checked ) {
			var score = $(this).val();
			$(this).parents(".question-item").attr("data-answered",$(this).val());
			$(this).parents(".question-item").removeClass('has-error');
			var field = 'SGQuestionIndex_' + index;
			Cookies.set(field, score);
			Cookies.set('SGCurrentPage', parentPageID);
		}
	});

	/* Get Cookies */
	// var answeredQuestions = Cookies.get('SGQuestionIndex');
	// console.log(answeredQuestions);
	var currentPage = Cookies.get('SGCurrentPage');

	if(currentPage!='undefined' && currentPage) {
		$(".questions-group").removeClass("animated fadeIn active");
		$(".questions-group#" + currentPage).addClass("animated fadeIn active");
	}

	var k=1; $(".question-item").each(function(){
		var target = $(this);
		var parentPage = $(this).parents(".questions-group");
		var answeredQuestions = Cookies.get('SGQuestionIndex_'+k);
		if(answeredQuestions!=undefined) {
			target.find("input.choice-input").each(function(){
				var option = $(this).val();
				if(answeredQuestions===option) {
					$(this).prop("checked",true);
					$(this).attr("checked",true);
					target.attr("data-answered",option);
				}
			});
		}
		k++;
	});

	/* Clear all the answers once submitted */
	function clear_all_answers() {
		var k=1; 
		$(".question-item").each(function(){
			$(this).find("input.choice-input").each(function(){
				Cookies.remove('SGQuestionIndex_'+k);
			});
			k++;
		});
		Cookies.remove('SGCurrentPage');
	}

	$(document).on("click",".buttonNext",function(e){
		e.preventDefault();
		var parent = $(this).parents(".questions-group");
		var next = $(this).attr("data-next");
		var errors = [];
		parent.find(".question-item").each(function(){
			var target = $(this);
			var k = $(this).attr("data-index");
			var answer = $(this).attr("data-answered");
			if(answer=='') {
				errors.push(k);
				target.addClass("has-error");
			} 
		});

		//var errors = [];
		if(errors.length>0) {
			$("#response").html(errorMessage);
			$("#response").addClass("fadeIn");
		} else {
			$("#response").html("");
			$("#response").removeClass("fadeIn");
			$(".questions-group").removeClass("fadeIn active");
			$(next).addClass("fadeIn active");
			$(".intro").addClass("hide");
			
			$('html, body').animate({
        scrollTop: $("#top").offset().top
      }, 1000, function() {
        var $target = $("#top");
        $target.focus();
        if ($target.is(":focus")) { // Checking if the target was focused
          return false;
        } else {
          $target.attr('tabindex','-1'); // Adding tabindex for elements not focusable
          $target.focus(); // Set focus again
        };
      });

		}

	});


	$(document).on("click",".buttonPrev",function(e){
		e.preventDefault();
		var prev = $(this).attr("data-prev");
		if( $(prev).length>0 ) {
			$(".questions-group").removeClass("fadeIn active");
			$(prev).addClass("fadeIn active");
		}
	});

	/* Check if URL has hash */
	if(window.location.hash) {
		var hash = window.location.hash;
		if( $(hash).length>0 ) {
			$(".questions-group").removeClass("fadeInLeft active");
			$(hash).addClass("fadeInLeft active");
			var num = hash.replace("#page-","");
			var prev = parseInt(num) - 1;
			/* Validate if the questions are answered */
			var errors = [];
			if(prev>0) {
				
				$("#page-"+prev).find(".question-item").each(function(){
					var target = $(this);
					var k = target.attr("data-index");
					$(this).find("input.choice-input").each(function(){
				 		if( this.checked ) {
				 			
				 		} else {
				 			target.addClass("has-error");
				 			errors.push(k);
				 		}
					});
				});

				//console.log(errors);

				if(errors.length > 0) {
					var pagelink = siteURL + "#page-" + prev;
					history.pushState('', document.title, pagelink);
					$(".questions-group").removeClass("fadeInLeft active");
					$("#page-"+prev).addClass("fadeInLeft active");
					$("#response").html(errorMessage);
					$("#response").addClass("fadeIn");
				}

			}
			
		}
	}

	/* Submit Test */
	$(document).on("click","#submitFormBtn",function(e){
		e.preventDefault();
		//$("#questionsForm").submit();
		var form = $("#questionsForm");
    var formAction = form.attr('action');
    var hasErrors = [];
    $(".question-item").each(function(k,v){
    	var target = $(this);
    	var choices = $(this).find("input.choice-input");
    	var isChecked = [];
    	choices.each(function(){
    		if( $(this).is(':checked') ) {
    			isChecked.push( $(this).val() );
    		} 
    	});
    	if( isChecked.length==0 ) {
    		hasErrors.push(k);
    		target.addClass("has-error");
    	}
    });


    if( hasErrors.length==0 ) {

			$.ajax({
					type: "POST",
					url: formAction,
					data: form.serialize(),
					dataType: "JSON",
					beforeSend:function(){
						var loaderText = 'Generating your result...<br>Do not close your web browser.';
						$(".loader-text").html(loaderText);
						$("#loader").show();
					},
					success: function(data) {
						var message = data.message;
						if(data.result) {
							clear_all_answers();
							var result_page = data.result_page;
							var completed_url = data.completedURL;
							window.location.href = completed_url;
							//history.pushState('', document.title, completed_url);
							// $("#main-content").load(result_page + " #thankYou",function(){
							// 	history.pushState('', document.title, completed_url);
							// });
							$("#loader").hide();
							$(".loader-text").html("");
						} else {
							$("#loader").hide();
							$(".loader-text").html("");
						}
					},
					complete:function(data){
						
					},
					error: function (xhr, desc, err) {
						$("#loader").hide();
						$(".loader-text").html("");
						if(xhr.responseText) {
							$("body").append('<div style="color:red;">'+xhr.responseText+'</div>');
						}
		      }
			});

    } else {
    	$("#response").html(errorMessage);
			$("#response").addClass("fadeIn");
    }

	});

	// $('a[href*="#"]')
	//   // Remove links that don't actually link to anything
	//   .not('[href="#"]')
	//   .not('[href="#0"]')
	//   .click(function(event) {
	//     // On-page links
	//     if (
	//       location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') 
	//       && 
	//       location.hostname == this.hostname
	//     ) {
	//       // Figure out element to scroll to
	//       var target = $(this.hash);
	//       target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
	//       // Does a scroll target exist?
	//       if (target.length) {
	//         // Only prevent default if animation is actually gonna happen
	//         event.preventDefault();
	//         $('html, body').animate({
	//           scrollTop: target.offset().top
	//         }, 1000, function() {
	//           // Callback after animation
	//           // Must change focus!
	//           var $target = $(target);
	//           $target.focus();
	//           if ($target.is(":focus")) { // Checking if the target was focused
	//             return false;
	//           } else {
	//             $target.attr('tabindex','-1'); // Adding tabindex for elements not focusable
	//             $target.focus(); // Set focus again
	//           };
	//         });
	//       }
	//     }
	//   });

});