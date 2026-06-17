# Summary Ranges — Technical Notes

## Análisis de Complejidad

- **Tiempo: `O(n)`** — El algoritmo realiza una única pasada lineal sobre el arreglo de entrada. El bucle `for` itera exactamente `n` veces (desde `i = 1` hasta `i <= nums.length`), y cada iteración realiza un número constante de operaciones de comparación y construcción de cadenas. No existe ningún bucle anidado ni recursividad.

- **Espacio: `O(n)`** — El único espacio auxiliar significativo es el arreglo `result`, que en el peor caso (cuando ningún elemento es consecutivo, ej. `[1, 3, 5, 7]`) almacena `n` cadenas de un solo elemento. No se utilizan estructuras de datos adicionales como Maps, Sets ni pilas de llamada recursiva. El espacio es proporcional a la salida, por lo que se clasifica como `O(n)` output space.

---

## Intuición y Enfoque

La técnica utilizada es **Sliding Window de inicio fijo** (o "anchor pointer"), una variante del patrón de dos punteros donde se ancla el inicio de una secuencia y se avanza hasta detectar una ruptura.

**Lógica central:**
1. Se registra el inicio (`start`) de la secuencia consecutiva actual con el primer elemento del arreglo.
2. Se itera desde el segundo elemento. En cada paso se comprueba si el elemento actual rompe la continuidad (`nums[i] !== nums[i-1] + 1`) o si se llegó al final del arreglo (`i === n`).
3. Al detectar una ruptura, se consolida el rango: si `start === nums[i-1]`, el rango es un elemento único (`"n"`); de lo contrario, es un rango compuesto (`"a->b"`).
4. Se actualiza `start` al nuevo elemento para comenzar el siguiente rango.

**¿Por qué es óptima?** La fuerza bruta podría intentar comparar todos los pares posibles en `O(n²)`. Esta solución aprovecha el hecho de que el arreglo ya viene **ordenado y sin duplicados** (precondición del problema), lo que garantiza que solo hace falta una pasada lineal para agrupar consecutivos. No requiere estructuras adicionales de búsqueda como un `HashSet` o un `Map`.

---

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript:** Se utilizan **template literals** (`` `${start}->${nums[i-1]}` ``) para construir las cadenas de rango de forma expresiva e inline, sin necesidad de concatenación explícita. La verificación del límite del arreglo (`i === nums.length`) aprovecha la naturaleza de que acceder a `nums[nums.length]` devuelve `undefined`, pero la condición de guarda `i === nums.length` previene el acceso fuera de rango antes de que ocurra. El array `result` es dinámico por naturaleza y `.push()` opera en `O(1)` amortizado.

- **PHP:** Al no disponer de template literals, la construcción del string de rango se realiza mediante el **operador de concatenación (`.`)**: `$start . "->" . $nums[$i - 1]`. Un detalle relevante es el cast explícito `(string)$start` para el caso de rango único, necesario para garantizar que el arreglo de retorno sea estrictamente de tipo `String[]` según la firma del método, ya que PHP tipifica los enteros de forma nativa y el juez de LeetCode puede ser estricto con el tipo. Se usa `count($nums)` para obtener la longitud y `$result[] = ...` como azúcar sintáctica equivalente a `array_push`.

---

## Lecciones Clave

- **Patrón Anchor Pointer sobre arreglos ordenados:** Cuando el problema implica agrupar, segmentar o "colapsar" secuencias contiguas en un arreglo ya ordenado, el patrón de anclar un puntero de inicio (`start`) y escanear hacia adelante es la herramienta ideal. Evita la necesidad de estructuras de búsqueda auxiliares y logra la solución óptima `O(n)` directamente. Aplicar este patrón cuando se trabaje con *intervals*, *merge ranges*, o *run-length encoding*.

- **La precondición del problema es la clave de la optimización:** La solución `O(n)` solo es posible porque el enunciado garantiza que `nums` está ordenado y no contiene duplicados. Siempre leer las restricciones del problema antes de diseñar la solución; una precondición de ordenamiento puede transformar un problema `O(n²)` en `O(n)` sin necesidad de ordenar previamente.
