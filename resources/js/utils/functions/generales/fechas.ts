export const sumarDiasSinDomingo=(fecha: Date, dias: number): Date=> {
  const resultado = new Date(fecha);
  let diasSumados = 0;

  while (diasSumados < dias) {
    resultado.setDate(resultado.getDate() + 1);
    if (resultado.getDay() !== 0) {
      diasSumados++;
    }
  }

  return resultado;
}