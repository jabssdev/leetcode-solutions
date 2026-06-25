# Range Sum Query - Immutable — Technical Notes

> **LeetCode #303** · Difficulty: Easy · Topic: Array, Design, Prefix Sum

---

## Análisis de Complejidad

### Constructor / Fase de Preprocesamiento

| Dimensión   | Notación | Justificación                                                                                                                                      |
| ----------- | -------- | -------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Tiempo**  | `O(n)`   | Un único bucle recorre los `n` elementos del array de entrada para construir el array de prefijos. Cada elemento se suma una sola vez en `O(1)`.   |
| **Espacio** | `O(n)`   | Se aloja un array auxiliar `prefix` de tamaño `n + 1` para almacenar las sumas acumuladas. Este es el único costo adicional de memoria del diseño. |

### Método `sumRange` / Fase de Consulta

| Dimensión   | Notación | Justificación                                                                                                                                         |
| ----------- | -------- | ----------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Tiempo**  | `O(1)`   | La consulta se resuelve con exactamente **dos accesos indexados** y **una resta**: `prefix[right + 1] - prefix[left]`. No hay bucles ni recursividad. |
| **Espacio** | `O(1)`   | No se crean estructuras adicionales en tiempo de consulta. La operación es puramente aritmética sobre el estado precalculado.                         |

> **Trade-off de diseño:** Se invierte `O(n)` de tiempo y espacio una única vez en el constructor para obtener `O(1)` garantizado en cada consulta posterior. Este es el principio central del patrón **Precomputation**.

---

## Intuición y Enfoque

### Técnica: Prefix Sum (Suma de Prefijos)

La fuerza bruta para `sumRange(left, right)` consistiría en iterar desde `left` hasta `right` acumulando elementos en cada consulta: `O(n)` por llamada. Con `k` consultas, el costo total escala a `O(n·k)`, lo que es inviable cuando ambos son grandes.

La solución óptima se basa en la siguiente identidad matemática:

$$\text{sumRange}(l, r) = \sum_{i=0}^{r} \text{nums}[i] \;-\; \sum_{i=0}^{l-1} \text{nums}[i]$$

Se construye un array `prefix` donde cada posición almacena la suma acumulada desde el índice `0` hasta `i - 1`:

$$\text{prefix}[i] = \sum_{j=0}^{i-1} \text{nums}[j]$$

La convención de **tamaño `n + 1` con centinela `prefix[0] = 0`** en el índice inicial es el detalle más importante del diseño: permite calcular cualquier rango sin manejar casos especiales para `left = 0`, ya que `prefix[0] = 0` actúa como un neutro de la suma.

Con esta estructura, cualquier consulta se reduce a:

$$\text{sumRange}(l, r) = \text{prefix}[r + 1] - \text{prefix}[l]$$

Una resta de dos valores ya calculados: complejidad `O(1)` constante e independiente del tamaño del rango consultado.

---

## Notas Políglotas (JavaScript vs PHP)

### JavaScript

- El constructor usa `new Int32Array(nums.length + 1)` en lugar de un array literal `[]`. `Int32Array` es un **TypedArray** del estándar Web APIs / ECMAScript: un buffer de memoria contigua y tipada que garantiza que todos sus elementos son enteros de 32 bits con signo, inicializados a `0` automáticamente. Esto lo hace más eficiente en memoria y acceso que un array de JS genérico (que es un objeto hash internamente).
- El `0` del centinela no necesita asignarse explícitamente gracias a que `Int32Array` inicializa todos sus slots a `0` por especificación, a diferencia de un array de JS normal donde los slots no inicializados serían `undefined`.
- El método `sumRange` se define sobre `NumArray.prototype`, siguiendo el patrón de **herencia prototípica** de JS pre-ES6. Es funcionalmente equivalente a definirlo dentro de una `class`, pero muestra de forma explícita cómo funciona la cadena de prototipos.

### PHP

- El constructor `__construct` inicializa `$this->prefix` como `[0]`, un array PHP con el centinela ya incluido. PHP no tiene TypedArrays, por lo que los arrays son estructuras dinámicas de tipo mixto internamente (hash maps ordenados), pero el docblock `@param Integer[] $nums` indica la intención de tipado.
- Se utiliza `$currentSum` como acumulador escalar en lugar de acceder a `$this->prefix[$i]` en cada iteración. Esta es una micro-optimización válida: reduce los accesos indirectos a través de `$this->prefix` y mantiene la suma en una variable de stack local.
- La construcción usa `foreach ($nums as $num)` con `$this->prefix[] = $currentSum` (append dinámico sin índice explícito). Este idioma PHP es equivalente a `array_push` pero más eficiente, ya que PHP asigna el siguiente índice entero disponible automáticamente, construyendo el array de prefijos de forma declarativa sin gestión manual de índices.
- `$this->prefix[$right + 1] - $this->prefix[$left]` en `sumRange` es sintácticamente idéntico a la versión JS, lo que confirma que la lógica central del algoritmo es agnóstica al lenguaje.

---

## Lecciones Clave

- **Patrón "Precomputation / Offline Processing":** Cuando un sistema recibirá múltiples consultas sobre un conjunto de datos estático (inmutable), siempre evaluar si existe una estructura precalculada que amortice el costo. El Prefix Sum es la instancia más pura de este patrón: pagar `O(n)` una sola vez en setup para obtener `O(1)` por consulta de forma indefinida. Este mismo principio aparece en tablas de hash precalculadas, índices de bases de datos, lookup tables en rendering, y caché de resultados de funciones puras.

- **El centinela como eliminador de casos borde:** Inicializar `prefix` con un `0` en la posición `0` y desplazar todos los valores un índice hacia la derecha (`prefix[i+1] = prefix[i] + nums[i]`) es una técnica general de diseño de arrays que elimina la necesidad de guardas `if (left == 0)`. Aplicar este patrón de **dummy node / sentinel value** reduce la complejidad ciclomática del código y previene off-by-one errors, siendo igualmente útil en listas enlazadas con nodo cabecera, algoritmos de DP con fila/columna base, y parsers con tokens de inicio/fin.
