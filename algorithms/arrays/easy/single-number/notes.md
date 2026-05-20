# Single Number

## Análisis de Complejidad

- **Tiempo**: $O(N)$ donde $N$ es la longitud del arreglo `nums` -> El algoritmo realiza un único recorrido lineal sobre el arreglo (`for...of` en JavaScript y `foreach` en PHP). En cada iteración, se ejecuta una sola operación XOR bit a bit (`^=`), la cual es una de las instrucciones más rápidas que puede ejecutar un procesador moderno (1 ciclo de reloj en la mayoría de las arquitecturas). No hay bucles anidados, llamadas recursivas ni operaciones de búsqueda, resultando en una complejidad temporal estrictamente lineal.
- **Espacio**: $O(1)$ -> El algoritmo es óptimo absoluto en consumo de memoria. Solo se utiliza una única variable escalar acumuladora (`result`) para mantener el estado del XOR progresivo. No se crean arreglos auxiliares, conjuntos, mapas, pilas ni ninguna otra estructura de datos, garantizando un espacio constante independientemente del tamaño de la entrada.

## Intuición y Enfoque

El problema establece que en un arreglo donde cada elemento aparece exactamente **dos veces** excepto uno que aparece una sola vez, debemos encontrar ese elemento único. LeetCode requiere explícitamente una solución con complejidad temporal lineal y sin espacio extra.

Un enfoque con Hash Map (registrar frecuencias de cada elemento) resolvería el problema en $O(N)$ de tiempo pero con $O(N)$ de espacio. Ordenar el arreglo y buscar el elemento sin pareja tomaría $O(N \log N)$ de tiempo. Ambos enfoques violan las restricciones óptimas del problema.

La solución implementa una técnica de **Manipulación de Bits (Bitwise XOR)** que explota tres propiedades algebraicas fundamentales del operador XOR ($\oplus$):

1. **Propiedad de Auto-Cancelación**: $a \oplus a = 0$. Cualquier número XOR consigo mismo se anula completamente, produciendo cero. Esto elimina automáticamente todos los pares de duplicados.
2. **Elemento Neutro (Identidad)**: $a \oplus 0 = a$. Cualquier número XOR con cero retorna el número original. Esto garantiza que, tras anularse todos los pares, el resultado contiene exclusivamente el valor del elemento único.
3. **Conmutatividad y Asociatividad**: $a \oplus b = b \oplus a$ y $(a \oplus b) \oplus c = a \oplus (b \oplus c)$. El orden en que se procesan los elementos es irrelevante; el resultado final es siempre el mismo.

Ejemplo conceptual: `[4, 1, 2, 1, 2]` → $4 \oplus 1 \oplus 2 \oplus 1 \oplus 2 = 4 \oplus (1 \oplus 1) \oplus (2 \oplus 2) = 4 \oplus 0 \oplus 0 = 4$.

El algoritmo simplemente inicializa `result = 0` y aplica XOR acumulativo con cada elemento del arreglo. Al finalizar, `result` contiene el valor del elemento sin pareja. La solución es profundamente elegante: alcanza simultáneamente los óptimos absolutos de tiempo $O(N)$ y espacio $O(1)$.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Operador XOR Bit a Bit (`^=`)**: JavaScript soporta nativamente operaciones bitwise sobre enteros. Cuando se aplica `^=`, el motor V8 convierte internamente los operandos a enteros con signo de 32 bits (Int32) para realizar la operación a nivel de registros del procesador, y luego almacena el resultado como un `Number` de doble precisión IEEE 754.
  - **Compacidad Máxima**: El cuerpo del bucle `for (const num of nums) result ^= num;` se expresa en una sola línea sin llaves, aprovechando la sintaxis de sentencia única de JavaScript para lograr una implementación minimalista y altamente expresiva.
  - **Iterador `for...of`**: Se utiliza el iterador moderno de ES6 para recorrer directamente los valores del arreglo sin necesidad de gestionar índices numéricos.

- **PHP**:
  - **Operador XOR Nativo (`^=`)**: PHP también soporta el operador XOR bit a bit de forma nativa con la misma semántica que JavaScript. Sin embargo, PHP opera con enteros nativos de la plataforma (64 bits en sistemas de 64 bits), lo que ofrece un rango de operación más amplio que los 32 bits a los que JavaScript trunca internamente para operaciones bitwise.
  - **Iteración con `foreach`**: Se emplea `foreach ($nums as $num)` para iterar directamente sobre los valores del arreglo, manteniendo la misma estructura compacta de una sola línea sin llaves que la versión en JavaScript.
  - **Sin Paso por Referencia**: A diferencia de otros problemas donde la modificación *in-place* del arreglo exige el operador `&`, aquí la función es puramente de lectura sobre `$nums`. No se modifica el arreglo original, por lo que el paso por valor predeterminado de PHP es completamente adecuado y no dispara el mecanismo de *copy-on-write*.

## Lecciones Clave

- **Manipulación de Bits como Herramienta de Eliminación de Estado**: La operación XOR es un mecanismo de cancelación perfecta: dos aplicaciones del mismo valor se anulan sin dejar rastro. Este patrón debe considerarse siempre que un problema involucre detección de elementos impares en frecuencia, paridad, o diferencias simétricas en conjuntos. Variantes más avanzadas (como *Single Number II* donde cada elemento aparece tres veces, o *Single Number III* con dos elementos únicos) extienden esta misma base conceptual con técnicas bitwise adicionales.
- **Propiedades Algebraicas como Algoritmos**: Este ejercicio demuestra que el conocimiento profundo de las propiedades matemáticas de los operadores primitivos (conmutatividad, asociatividad, auto-cancelación) puede reemplazar por completo la necesidad de estructuras de datos auxiliares. En escenarios de sistemas embebidos, procesamiento de señales, o entornos con restricciones extremas de memoria, las operaciones bitwise son una herramienta indispensable que todo ingeniero de software debe dominar.
