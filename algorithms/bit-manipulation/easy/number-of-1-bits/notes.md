# Number of 1 Bits (Hamming Weight)

## Análisis de Complejidad

- **Tiempo**: $O(K)$ donde $K$ es el número de bits establecidos en `1` dentro de la representación binaria de `n` -> El bucle `while` se ejecuta exactamente $K$ veces, una vez por cada bit `1` presente en el número. En cada iteración, la operación `n &= n - 1` elimina precisamente el bit `1` de menor peso (_lowest set bit_), reduciendo el contador de bits restantes en uno. En el peor caso (por ejemplo, $n = 2^{32} - 1$, donde los 32 bits son `1`), $K = 32$, lo cual es una constante acotada. Por lo tanto, la complejidad también puede expresarse como $O(1)$ en términos del tamaño fijo de un entero de 32 bits.
- **Espacio**: $O(1)$ -> El algoritmo solo utiliza una variable escalar acumuladora (`count`) y opera directamente sobre el valor de entrada `n` mediante mutación in-place. No se crean estructuras de datos auxiliares, arreglos, cadenas de bits ni se usa recursión, garantizando un consumo de memoria constante.

## Intuición y Enfoque

El problema solicita contar la cantidad de bits `1` en la representación binaria de un entero sin signo (lo que se conoce formalmente como el **Peso de Hamming** o _population count_).

Un enfoque ingenuo sería iterar sobre los 32 bits del número, desplazando un bit a la derecha en cada paso (`n >> 1`) y verificando el bit menos significativo con `n & 1`. Esto siempre ejecuta exactamente 32 iteraciones, independientemente de cuántos bits `1` tenga el número.

La solución implementa el **Truco de Brian Kernighan (Brian Kernighan's Bit Trick)**, una técnica de **Manipulación de Bits (Bitwise)** significativamente más elegante y eficiente:

La operación clave es `n &= n - 1`. Su efecto es **eliminar el bit `1` de menor peso** (_lowest set bit_) en cada iteración. La demostración matemática es la siguiente:

1. Restar `1` a un número binario invierte todos los bits desde el bit `1` de menor peso hacia la derecha (incluyéndolo a él mismo). Por ejemplo: `n = 1100` → `n - 1 = 1011`.
2. Al aplicar AND bit a bit entre `n` y `n - 1`, los bits invertidos por la resta se anulan, eliminando exactamente el bit `1` más bajo: `1100 & 1011 = 1000`.
3. El bucle se repite hasta que `n` se convierte en `0` (no quedan bits `1`), y el contador `count` registra cuántas eliminaciones fueron necesarias.

Esta técnica es óptima porque el número de iteraciones es exactamente igual al número de bits `1` ($K$), no al ancho total del entero (32). Para números dispersos (con pocos bits `1`), el rendimiento es dramáticamente superior al enfoque de iteración fija de 32 pasos.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Operador AND Bit a Bit (`&=`)**: JavaScript convierte internamente los operandos a enteros con signo de 32 bits (Int32) antes de ejecutar cualquier operación bitwise. Para este problema, LeetCode pasa un entero sin signo, pero la conversión a Int32 no afecta la corrección del algoritmo de Kernighan ya que la operación `n &= n - 1` funciona idénticamente sobre la representación de bits independientemente de la interpretación del signo.
  - **Comparación Estricta (`!==`)**: Se utiliza `n !== 0` en la condición del `while` para evitar la coerción implícita de tipos. Si se usara `n != 0`, valores como `null`, `undefined` o cadenas vacías podrían pasar inadvertidos en contextos no controlados, aunque en este problema los tipos están garantizados por LeetCode.
  - **Mutación Directa del Parámetro**: La variable `n` se modifica directamente en cada iteración (`n &= n - 1`). En JavaScript, los números son tipos primitivos pasados por valor, por lo que esta mutación no afecta al valor original del llamador.

- **PHP**:
  - **Enteros de 64 Bits**: En plataformas de 64 bits (la mayoría de los servidores modernos), PHP utiliza enteros nativos de 64 bits (`zend_long`). A diferencia de JavaScript, donde las operaciones bitwise truncan a 32 bits, PHP opera sobre el ancho completo del entero de la plataforma. Para los propósitos de este problema (enteros sin signo de 32 bits), el comportamiento es correcto ya que los 32 bits superiores son ceros.
  - **Isomorfismo Total**: La solución en PHP es estructuralmente idéntica a la de JavaScript: mismo bucle `while`, misma operación `$n &= $n - 1`, mismo contador `$count`. El operador `&=` de PHP tiene la misma semántica bitwise que en JavaScript, y `!==` realiza comparación estricta de valor y tipo. Esto demuestra que las operaciones bitwise son uno de los dominios más portables entre lenguajes de programación.
  - **Paso por Valor Nativo**: Los enteros en PHP son tipos escalares pasados por valor de forma predeterminada. La mutación de `$n` dentro de la función no requiere el operador de referencia `&` en la firma, y no afecta al valor original del llamador.

## Lecciones Clave

- **El Truco de Brian Kernighan como Primitiva de Manipulación de Bits**: La operación `n & (n - 1)` para eliminar el _lowest set bit_ es una primitiva fundamental que trasciende este problema individual. Se aplica directamente en detección de potencias de 2 (`n & (n - 1) === 0` si y solo si `n` es potencia de 2), en algoritmos de _population count_ para procesamiento de imágenes y criptografía, en la implementación de Árboles de Fenwick (Binary Indexed Trees), y en cualquier escenario donde necesitemos iterar exclusivamente sobre los bits activos de una máscara de bits (_bitmask_).
- **Iteración Proporcional a la Densidad, No al Tamaño**: Este ejercicio enseña un principio de diseño poderoso: cuando es posible, un algoritmo debe escalar con la **densidad de la información relevante** (los bits `1` en este caso) y no con el tamaño bruto del contenedor (los 32 bits totales). Este mismo principio se aplica en el procesamiento de matrices dispersas (_sparse matrices_), grafos con pocos aristas, o cualquier estructura donde la mayoría de los datos son triviales (ceros, nulos, vacíos) y solo los elementos no triviales merecen procesamiento.
