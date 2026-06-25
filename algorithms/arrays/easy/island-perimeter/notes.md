# Island Perimeter — Technical Notes

> **LeetCode #463** · Difficulty: Easy · Topic: Array, Depth-First Search, Breadth-First Search, Matrix

---

## Análisis de Complejidad

Donde `R = grid.length` (filas) y `C = grid[0].length` (columnas):

| Dimensión   | Notación   | Justificación                                                                                                                                                                                                                                            |
| ----------- | ---------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Tiempo**  | `O(R × C)` | Dos bucles anidados recorren cada celda de la matriz exactamente una vez. Por cada celda de tierra (`1`), se realizan a lo sumo 2 comparaciones de vecindad, ambas `O(1)`. No hay traversal de grafo, recursión, ni estructuras de búsqueda adicionales. |
| **Espacio** | `O(1)`     | Solo se utilizan variables escalares: `perimeter`, `rows`, `cols`, `r`, `c`. No se crean matrices auxiliares de visitados, stacks de DFS, ni colas de BFS. Es la solución de espacio mínimo posible para este problema.                                  |

---

## Intuición y Enfoque

### Técnica: Cell Contribution + Edge Cancellation (Reducción Geométrica Local)

El enfoque naïve resolvería este problema con DFS/BFS para encontrar la isla y luego contar bordes expuestos. Esto es correcto pero introduce `O(R × C)` de espacio en el Call Stack (DFS) o en la cola (BFS), y requiere un array de visitados.

La solución óptima observa que el perímetro total puede calcularse como la **suma de contribuciones individuales de cada celda de tierra**, sin necesidad de conocer la isla como entidad global.

**Principio geométrico fundamental:**

Cada celda de tierra `(r, c)` contribuye con exactamente **4 lados** al perímetro potencial (un cuadrado tiene 4 aristas). Sin embargo, por cada vecino adyacente que _también_ es tierra, el borde compartido entre ambas celdas **no pertenece al perímetro** — es un borde interior. Ese borde elimina 1 unidad de perímetro de cada celda, es decir **2 unidades en total** (una por cada celda que lo comparte).

$$\text{perimeter} = \sum_{\text{celdas con } 1} \left(4 - 2 \times \text{vecinos\_tierra}\right)$$

**Optimización crítica — inspección solo hacia arriba y hacia la izquierda:**

En lugar de verificar los 4 vecinos de cada celda, el algoritmo verifica únicamente **2**: el vecino superior `(r-1, c)` y el vecino izquierdo `(r, c-1)`. Esto es suficiente y correcto por la siguiente razón:

Al recorrer la grid de izquierda a derecha y de arriba a abajo, cada par de celdas vecinas es detectado **exactamente una vez** desde la celda que los procesa "después" en el orden de recorrido. La celda inferior detecta la adyacencia con la superior, y la celda derecha detecta la adyacencia con la izquierda. Los vecinos derecho e inferior aún no han sido procesados y se auto-reportarán cuando llegue su turno. Restar `2` en ese punto único cubre ambas deducciones (la de la celda actual y la del vecino) en una sola operación.

**Demostración con un ejemplo:**

```
Grid:  [1, 1]
       [0, 1]

Celda (0,0): +4 → perimeter = 4
Celda (0,1): +4, vecino izq (0,0)=1 → -2 → perimeter = 6
Celda (1,0): grid=0, skip
Celda (1,1): +4, vecino sup (0,1)=1 → -2 → perimeter = 8
Resultado: 8 ✓
```

---

## Notas Políglotas (JavaScript vs PHP)

### JavaScript

- `grid.length` y `grid[0].length` se pre-calculan en `const rows` y `const cols` antes de los bucles. El uso de `const` es semánticamente correcto: estas dimensiones son invariantes durante toda la ejecución. Evitar accesos repetidos a `.length` dentro de la condición del `for` es una micro-optimización de legibilidad y potencialmente de rendimiento en engines que no optimizan agresivamente la propagación de constantes.
- La comparación `grid[r][c] === 1` usa igualdad estricta, necesaria en JS para distinguir el entero `1` del string `"1"` u otros valores truthy. Dado que la entrada es `number[][]` por el docblock, es correcta y segura.
- Las condiciones de vecindad `r > 0 && grid[r-1][c] === 1` y `c > 0 && grid[r][c-1] === 1` utilizan **short-circuit evaluation**: si `r === 0` (primera fila), la segunda parte de la condición no se evalúa, evitando un acceso fuera de rango `grid[-1]` que retornaría `undefined` y causaría una comparación incorrecta (`undefined === 1` es `false`, lo que sería accidentalmente correcto, pero acceder a `grid[-1][c]` lanzaría un TypeError al intentar indexar `undefined`).
- La operación `perimeter += 4` seguida de `perimeter -= 2` mantiene el acumulador como una sola variable escalar mutable (`let`), reflejando el cómputo incremental celda por celda.

### PHP

- `count($grid)` y `count($grid[0])` se pre-calculan en `$rows` y `$cols` antes de los bucles. Este patrón es más importante en PHP que en JS: `count()` es una llamada a función con overhead de invocación, mientras que `.length` en JS es una propiedad de acceso directo. Pre-calcular evita que el intérprete evalúe `count()` en cada iteración de la condición del `for` anidado.
- La comparación `$grid[$r][$c] === 1` usa strict comparison de PHP (`===`), que verifica tipo y valor simultáneamente. Para `Integer[][]` (declarado en el docblock), es equivalente a `==` en este contexto, pero el uso de `===` es la práctica correcta para evitar comparaciones laxas con `true` o `"1"`.
- Las condiciones de bounds checking `$r > 0` y `$c > 0` son igualmente necesarias en PHP. A diferencia de JS donde acceder a `grid[-1]` retorna `undefined`, PHP con índices negativos en arrays asociativos puede comportarse de forma impredecible: `$grid[-1]` retornaría `null` si la clave `-1` no existe, pero `$grid[-1][$c]` lanzaría un warning de "Trying to access array offset on null". El guard `$r > 0` previene esto de forma limpia.
- La sintaxis `$perimeter += 4` y `$perimeter -= 2` es funcionalmente idéntica a JS. PHP trata los enteros con aritmética nativa sin riesgo de overflow para los valores posibles en este problema (máximo `R × C × 4 = 100 × 100 × 4 = 40000`), muy por debajo del límite de `PHP_INT_MAX`.

---

## Lecciones Clave

- **Patrón "Cell Contribution / Local Reduction":** Muchos problemas sobre matrices o grafos que parecen requerir traversal global (DFS/BFS) pueden resolverse mediante una **suma de contribuciones locales** si existe una propiedad aditiva que se puede calcular celda por celda sin contexto global. Antes de implementar un DFS, siempre evaluar si el resultado global es descomponible en contribuciones independientes por celda o nodo. Este patrón aparece en conteo de perímetros, áreas de regiones, detección de bordes en visión computacional, y cálculo de métricas de grafos (grado de nodos, densidad de aristas) sin traversal completo.

- **Inspección de vecindad parcial (2 de 4 direcciones) como optimización de simetría:** Verificar solo los vecinos ya procesados en el orden de recorrido (arriba e izquierda en un scan row-major) garantiza que cada par de adyacencias sea contabilizado exactamente una vez, evitando el doble conteo sin necesidad de un Set de aristas visitadas. Este principio de **procesar relaciones simétricas desde un solo lado** se aplica en algoritmos de grafos no dirigidos (contar aristas sin duplicados), en productos cartesianos (iterar solo la mitad superior de la matriz de distancias), y en algoritmos de programación dinámica donde las transiciones se definen solo en una dirección del espacio de estados.
