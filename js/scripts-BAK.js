jQuery(document).ready(function ($) {

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
			beforeSend:function(){
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
		if( this.checked ) {
			$(this).parents(".question-item").attr("data-answered",$(this).val());
			$(this).parents(".question-item").removeClass('has-error');
		}
	});

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


	// $(document).on("click",".buttonNext",function(e){
	// 	e.preventDefault();
	// 	var next = $(this).attr("data-next");
	// 	var prev = $(this).attr("data-prev");
	// 	var total = $(this).attr("data-count");
	// 	var last = $(this).attr("data-last");
	// 	//$(".questions-group").removeClass("fadeInLeft active");
	// 	var errors = [];
	// 	if( $(next).length>0 ) {
			
	// 		if( $(prev).length > 0 ) {
	// 			$(prev).find(".question-item").each(function(){
	// 				var target = $(this);
	// 				var k = $(this).attr("data-index");
	// 				var answer = $(this).attr("data-answered");
	// 				if(answer) {
	// 					// $(".questions-group").removeClass("fadeInLeft active");
	// 					// $(next).addClass("fadeInLeft active");
	// 				} else {
	// 					errors.push(k);
	// 					target.addClass("has-error");
	// 				}
	// 			});
	// 		}

	// 		if( errors.length > 0 ) {
	// 			$("#response").html(errorMessage);
	// 			$("#response").addClass("fadeIn");
	// 		} else {
	// 			$(".questions-group").removeClass("fadeInLeft active");
	// 			$(next).addClass("fadeInLeft active");
	// 			$("#response").html("");
	// 			$("#response").removeClass("fadeIn");
	// 		}

	// 	}

	// });

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