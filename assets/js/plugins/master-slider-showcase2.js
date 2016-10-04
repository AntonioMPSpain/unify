var MasterSliderShowcase2 = function () {

    return {

        //Master Slider
        initMasterSliderShowcase2: function () {
		    var slider = new MasterSlider();
		 
		    slider.control('arrows');  
		    slider.control('bullets' , {autohide:false, align:'bottom', margin:0});   
		 
		    slider.setup('masterslider' , {
		        width:400,
		        height:266,
		        layout:'partialview',
		        space:0,
		        autoplay:true,
		        speed:20,
		        view:'basic',
		        loop:true,
		        filters: {
		        	
        		}
		    });
        }
        
    };
    
}(); 