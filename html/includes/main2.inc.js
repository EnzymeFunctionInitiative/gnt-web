function expand(section)
{
  if (document.getElementById(section).style.display=='none')
  {
  document.getElementById(section).style.display='inline';
  }
  else
  {
  document.getElementById(section).style.display='none';
  }
}
function close_all()
{
  i = 1;
  while (document.getElementById('#' + i))
        {
        document.getElementById('#' + i).style.display='none';
        ++i;
        }
}
function show_all()
{
  i = 1;
  while (document.getElementById('#' + i))
        {
        document.getElementById('#' + i).style.display='inline';
        ++i;
        }
}


