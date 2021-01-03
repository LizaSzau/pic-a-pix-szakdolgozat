// *******************************************************************************
// MUTAT - táblázat sorainak elrejtése és megmutatása
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
// TÖRÖL - Mégesm ír a fórumba
// *******************************************************************************

function torol()
{
	document.getElementById("i_ir").style.display = 'none';
	document.getElementById("i_uzenet").value = '';
}

