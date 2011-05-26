function checkAll(cbclass,exby)
{
	var cbs = document.getElementsByClassName(cbclass);
	
	for ( var i=0; i<cbs.length; i++ )
	{
		cbs[i].checked = exby.checked? true:false
	}
}
