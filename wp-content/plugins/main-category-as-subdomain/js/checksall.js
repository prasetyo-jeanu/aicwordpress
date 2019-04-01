var subdomains = [];
jQuery('.subdomain:checkbox').each(function() {
							
				if ( this.checked && this.value != -1)
						subdomains.push(this.value);								
						
							
});


jQuery('#select-all').click(function(event) { 
				
					if(this.checked) {
						jQuery('.subdomain:checkbox').each(function() {								
						
							this.checked = true;
							
						});
						
						jQuery('span#thecheck').replaceWith('<span style="margin-left:40px;font:12px;color:#ccc" id="thecheck">Back</span>');
					
					} else {

						jQuery('.subdomain:checkbox').each(function() {

							if ( subdomains.indexOf( this.value ) < 0 )
									this.checked = false;
							
							
						});
					
						jQuery('span#thecheck').replaceWith('<span style="margin-left:40px;font:12px;color:#ccc" id="thecheck" >Check All</span>');
					}
									
					
					
					
					
});