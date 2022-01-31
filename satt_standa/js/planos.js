function gen_vehicu(formulario)
{
  validacion = true
  formulario = document.form_sel_plano
  formulario.opcion.value= 2;
  formulario.plano.value= 'MINMVEHI';
  formulario.submit();
}

function gen_person(formulario)
{
  validacion = true
  formulario = document.form_sel_plano
  formulario.opcion.value= 3;
  formulario.plano.value= 'MINMPERS';
  formulario.submit();
}

function gen_empres(formulario)
{
  validacion = true
  formulario = document.form_sel_plano
  formulario.opcion.value= 4;
  formulario.plano.value= 'MINMEMPR';
  formulario.submit();
}

function gen_mercan(formulario)
{
  validacion = true
  formulario = document.form_sel_plano
  formulario.opcion.value= 5;
  formulario.plano.value= 'MINMERCANC';
  formulario.submit();
}

function gen_manifi(formulario)
{
  validacion = true
  formulario = document.form_sel_plano
  formulario.opcion.value= 6;
  formulario.plano.value= 'MINHMANIFIESTO';
  formulario.submit();
}