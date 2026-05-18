# Contains Duplicate

## Análisis de Complejidad

- **Tiempo**: $O(N)$ -> En ambas soluciones, recorremos el arreglo de entrada de longitud $N$ exactamente una vez (`for...of` en JavaScript y `foreach` en PHP). En cada iteración, la búsqueda e inserción en la estructura hash (`Set.prototype.has` / `Set.prototype.add` en JavaScript e `isset` / asignación de clave en PHP) se realizan en tiempo constante promedio $O(1)$. En el peor de los casos, si no hay duplicados o si el duplicado se encuentra al final del arreglo, el algoritmo realiza $N$ iteraciones, resultando en una complejidad de tiempo lineal.
- **Espacio**: $O(N)$ -> En el peor escenario (cuando todos los elementos del arreglo son únicos), ambas soluciones almacenan los $N$ elementos en memoria. En JavaScript, el objeto `Set` mantendrá $N$ entradas, mientras que en PHP, el arreglo asociativo almacenará $N$ pares clave-valor. Esto resulta en una complejidad de espacio lineal directamente proporcional al tamaño de la entrada.

## Intuición y Enfoque

El objetivo de este problema es detectar la presencia de al menos un elemento duplicado en una colección. La fuerza bruta requeriría comparar cada elemento con todos los demás usando dos bucles anidados, lo que resultaría en una complejidad temporal ineficiente de $O(N^2)$. Alternativamente, podríamos ordenar el arreglo primero, reduciendo el tiempo a $O(N \log N)$ y el espacio a $O(1)$ (o $O(N)$ dependiendo del algoritmo de ordenación), pero comprometiendo el tiempo de ejecución.

El enfoque óptimo elegido utiliza una **tabla de dispersión (Hash Table / Set)**. La intuición detrás de esta técnica es la memorización de elementos ya procesados. A medida que recorremos el arreglo:
1. Comprobamos de manera instantánea si el elemento actual ya ha sido registrado en nuestra estructura hash.
2. Si ya existe, retornamos inmediatamente `true` (detección temprana de duplicados).
3. Si no existe, lo agregamos al conjunto y continuamos.

Esta técnica de *trade-off* (intercambio de memoria por velocidad) reduce la complejidad temporal de $O(N^2)$ a $O(N)$ a expensas de un uso de espacio auxiliar de $O(N)$, lo cual es ideal para conjuntos de datos grandes.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Uso de la clase nativa `Set`**: JavaScript provee un tipo de objeto nativo estructurado para almacenar valores únicos (`Set`), implementado eficientemente como una tabla hash a nivel de motor de JS (ej. V8).
  - **Sintaxis Explicativa**: La API del `Set` permite utilizar métodos autodescriptivos como `.has()` para verificar la membresía y `.add()` para insertar nuevos elementos, lo que resulta en un código altamente legible y semántico.
  - **Manejo de Tipos**: El `Set` en JavaScript diferencia tipos (por ejemplo, `5` y `"5"` son tratados como valores únicos diferentes), lo cual previene comportamientos inesperados si la entrada no estuviera tipada estrictamente.

- **PHP**:
  - **Simulación de Set con Arreglos Asociativos**: Al carecer PHP de una estructura de tipo `Set` nativa integrada de forma predeterminada en su núcleo estándar (sin recurrir a extensiones PECL como `Ds`), la forma idiomática y de alto rendimiento de simular un conjunto es utilizando un **arreglo asociativo** (`$seen = []`).
  - **Optimización mediante `isset()`**: Para verificar si un elemento ha sido visto, se utiliza `isset($seen[$num])`. Dado que `isset` es un constructor del lenguaje y no una función regular, es extremadamente rápido y eficiente a nivel de bytecode, superando a funciones como `array_key_exists()`.
  - **Coerción de Claves**: En los arreglos asociativos de PHP, las claves numéricas son tratadas directamente como enteros, y las cadenas numéricas son convertidas a enteros automáticamente. Para este problema enfocado en enteros (`Integer[] $nums`), este comportamiento nativo no genera conflictos, pero es un factor de diseño clave a tener en cuenta si se trabajara con otros tipos de datos.

## Lecciones Clave

- **La Técnica del Conjunto de Visitas (Visited Set Pattern)**: Es uno de los patrones más fundamentales en algoritmos de arreglos y grafos. Debe aplicarse siempre que necesitemos realizar un seguimiento de elementos previos para validar condiciones de unicidad, ciclos, o relaciones de coincidencia (por ejemplo, en *Two Sum* o detección de ciclos en listas enlazadas), logrando búsquedas en $O(1)$.
- **Trade-off de Espacio vs Tiempo**: Este problema ilustra perfectamente cómo la introducción de memoria adicional ($O(N)$ de espacio auxiliar) permite rebajar drásticamente la barrera de rendimiento temporal de un algoritmo de cuadrático a lineal. Es una decisión de diseño crucial en el software de producción, donde la memoria suele ser barata y el tiempo de respuesta del usuario es prioritario.
