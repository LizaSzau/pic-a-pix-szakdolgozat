// *******************************************************************************
// MUTAT - t�bl�zat sorainak elrejt�se �s megmutat�sa
// *******************************************************************************

function mutat(sh_this)
{
  if(document.getElementById(sh_this).style.display=='none')
  {
    document.getElementById(sh_this).style.display = '';
  }
  else
  {
    document.getElementById(sh_this).style.display = 'none';
  }
}

// *******************************************************************************
// T�R�L - M�gesm �r a f�rumba
// *******************************************************************************

function torol()
{
	document.getElementById("i_ir").style.display = 'none';
	document.getElementById("i_uzenet").value = '';
}

