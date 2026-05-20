# Two Sum

## Análisis de Complejidad

- **Tiempo**: $O(N)$ donde $N$ es la longitud del arreglo `nums` -> El algoritmo realiza un único recorrido lineal sobre el arreglo (`for` con índice en JavaScript, `foreach` con clave-valor en PHP). En cada iteración, se ejecutan tres operaciones fundamentales: el cálculo aritmético del complemento (`target - nums[i]`), la búsqueda de membresía en el mapa (`Map.prototype.has` / `isset`), y la inserción en el mapa (`Map.prototype.set` / asignación de clave). Todas estas operaciones se ejecutan en tiempo constante amortizado $O(1)$ gracias a la tabla hash subyacente, resultando en una complejidad temporal estrictamente lineal.
- **Espacio**: $O(N)$ -> En el peor escenario (cuando la solución involucra los dos últimos elementos del arreglo, o el arreglo es recorrido casi en su totalidad antes de encontrar el par), el mapa almacenará hasta $N - 1$ entradas de la forma `{valor → índice}`. Cada entrada ocupa espacio constante, por lo que el consumo total de memoria es lineal respecto al tamaño de la entrada.

## Intuición y Enfoque

El problema solicita encontrar los índices de exactamente dos elementos cuya suma sea igual a un valor objetivo `target`. La fuerza bruta requeriría dos bucles anidados que evalúen todas las combinaciones posibles de pares, resultando en una complejidad de $O(N^2)$.

La solución implementa la técnica de **Hash Map de Una Sola Pasada (One-Pass Hash Map)**, que transforma la pregunta central del problema de una búsqueda cuadrática a una búsqueda constante:

En lugar de preguntar *"¿existe algún otro elemento que sumado a `nums[i]` dé `target`?"* (lo cual requeriría recorrer el arreglo de nuevo), reformulamos la pregunta como: *"¿ya he visto anteriormente el complemento `target - nums[i]`?"*. Esta reformulación permite una búsqueda instantánea en la tabla hash.

1. Inicializamos un mapa vacío que almacenará pares `{valor → índice}` de los elementos ya visitados.
2. Para cada elemento `nums[i]`, calculamos su **complemento** aritmético: `complement = target - nums[i]`.
3. Verificamos si el complemento ya existe como clave en el mapa. Si existe, hemos encontrado el par: retornamos el índice almacenado del complemento y el índice actual `i`.
4. Si no existe, registramos el elemento actual en el mapa (`map[nums[i]] = i`) para que esté disponible como complemento potencial de algún elemento futuro.

El diseño de "buscar primero, insertar después" es intencionalmente estratégico: garantiza que nunca emparejemos un elemento consigo mismo, ya que un elemento solo se inserta en el mapa **después** de haber verificado que su complemento no estaba presente.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Estructura `Map` Nativa (ES6)**: Se utiliza la clase `Map` en lugar de un objeto plano (`{}`). `Map` ofrece ventajas significativas para este caso de uso: las claves pueden ser de cualquier tipo (incluyendo enteros negativos sin conversión a cadena), la búsqueda con `.has()` y la obtención con `.get()` son operaciones de tiempo constante $O(1)$ garantizado, y la API es semánticamente más clara que el acceso por corchete de objetos.
  - **Bucle con Índice Explícito**: Se emplea un bucle `for (let i = 0; i < nums.length; i++)` clásico con índice numérico. Esto es necesario porque el problema requiere retornar los **índices** de los elementos, y el iterador `for...of` no expone el índice de forma nativa sin recurrir a `.entries()`.
  - **Retorno Implícito de `undefined`**: Si no se encuentra ningún par válido, la función no tiene un `return` explícito al final, retornando `undefined` implícitamente. Esto es aceptable bajo la precondición de LeetCode que garantiza exactamente una solución válida.

- **PHP**:
  - **Arreglo Asociativo como Hash Map**: PHP simula la funcionalidad de un `Map` mediante un arreglo asociativo (`$map = []`), donde los valores numéricos del arreglo de entrada se utilizan como claves y los índices como valores. Internamente, los arreglos de PHP están implementados como tablas hash (estructura `zend_array`), proporcionando búsqueda e inserción en $O(1)$ amortizado.
  - **`foreach` con Acceso a Clave (`$i => $num`)**: PHP expone tanto el índice como el valor simultáneamente mediante la sintaxis `foreach ($nums as $i => $num)`, eliminando la necesidad de un contador de índice manual y produciendo un código más declarativo que la versión en JavaScript.
  - **Búsqueda con `isset()`**: Se utiliza `isset($map[$diff])` para verificar la existencia del complemento. Como constructor de lenguaje (no función), `isset` opera directamente sobre el bytecode del motor Zend con un rendimiento superior al de alternativas como `array_key_exists()`.
  - **Retorno Defensivo**: A diferencia de la versión en JavaScript, la solución en PHP incluye un `return []` explícito al final como salvaguarda defensiva. Aunque LeetCode garantiza una solución, esta práctica previene posibles errores de tipo en contextos de producción donde el tipado estricto de PHP podría generar advertencias por retorno `null` implícito.

## Lecciones Clave

- **El Patrón de Complemento con Hash Map (Complement Lookup)**: Este es posiblemente el patrón algorítmico más importante y recurrente en problemas de búsqueda de pares y subconjuntos. La idea central de reformular *"buscar un par que cumpla una condición"* como *"buscar si el complemento de la condición ya fue registrado"* transforma búsquedas cuadráticas en lineales. Se aplica directamente a variantes como *3Sum* (con un bucle exterior adicional), *4Sum*, *Two Sum II* (con Two Pointers en arreglo ordenado), *Subarray Sum Equals K* (con sumas acumulativas), y cualquier problema de emparejamiento basado en diferencias, productos o XOR.
- **Inserción Diferida como Mecanismo de Integridad**: La estrategia de verificar primero la existencia del complemento **antes** de insertar el elemento actual en el mapa es un patrón de diseño defensivo que previene auto-emparejamientos (usar el mismo elemento dos veces). Esta técnica de "consultar antes de registrar" es análoga al patrón de *check-then-act* en sistemas concurrentes y debe internalizarse como práctica estándar en algoritmos de búsqueda con estado acumulativo.
