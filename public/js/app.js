$('.delete').on('click', function() {
	return confirm('Are you sure? This image WILL BE DELETED');
});

$('#ban').on('click', function() {
	return confirm('Are you sure? This user will be BANNED and ALL OF THEIR IMAGES WILL BE DELETED');
});

$('#links li input').on('click', function()
{
	$(this).select();
});

$('body').toggleClass('no-js js');
