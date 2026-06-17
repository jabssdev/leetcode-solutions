# Implement Stack Using Queues — Technical Notes

## Análisis de Complejidad

| Operación | Tiempo     | Justificación |
|-----------|------------|---------------|
| `push`    | `O(n)`     | Tras enqueue del nuevo elemento, se rotan los `n-1` elementos anteriores al frente de la queue mediante un bucle `while`, dejando el último insertado en la cabeza. |
| `pop`     | `O(1)`     | El elemento en el tope ya está ubicado en la cabeza de la queue tras la rotación del `push`; un simple `shift`/`dequeue` lo extrae en tiempo constante. |
| `top`     | `O(1)`     | Acceso directo al índice `[0]` del array (JS) o al método `bottom()` de `SplQueue` (PHP). Sin recorrido. |
| `empty`   | `O(1)`     | Comparación directa del tamaño de la estructura interna. |

- **Espacio: `O(n)`** — La única estructura auxiliar es la queue interna que almacena `n` elementos como máximo, donde `n` es el número de elementos actualmente en el stack. No se utilizan queues secundarias, stacks adicionales ni recursividad que genere call stack extra. La variante implementada aquí es la denominada **"costosa en push, barata en pop"** con una sola queue.

---

## Intuición y Enfoque

La técnica utilizada es **Queue Rotation on Push** — una estrategia de simulación de estructuras de datos que resuelve la inversión de orden inherente entre una queue (FIFO) y un stack (LIFO) pagando el costo en la operación de inserción.

**Lógica central del `push`:**

Cuando se inserta un nuevo elemento `x` en una queue de tamaño `n`, este queda al final (posición `n`), pero el stack requiere que sea el primero en salir. La solución consiste en rotar los `n` elementos anteriores al frente, re-enqueuándolos uno a uno al final de la misma queue. Tras `n` rotaciones, `x` queda en la cabeza.

```
Estado inicial queue: [1, 2, 3]   push(4)
Después del enqueue:  [1, 2, 3, 4]
Rotación 1:           [2, 3, 4, 1]
Rotación 2:           [3, 4, 1, 2]
Rotación 3:           [4, 1, 2, 3]
→ pop()/top() retorna 4 ✓
```

**¿Por qué es óptima frente a la fuerza bruta (dos queues)?** La variante clásica con dos queues `Q1` y `Q2` logra el mismo resultado pero requiere el doble de espacio para la queue temporal de soporte. Esta implementación de **una sola queue** elimina esa estructura auxiliar, manteniendo la misma complejidad de tiempo `O(n)` en `push` con `O(1)` en `pop`, a costa de ningún overhead espacial adicional.

---

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript:** El array nativo actúa como queue gracias a la combinación de `.push()` (enqueue al final) y `.shift()` (dequeue desde el frente). La rotación en `push` se expresa de forma idiomática: `this.queue.push(this.queue.shift())` en una sola línea dentro del `while`. El contador de rotaciones se inicializa con `this.queue.length` **después** del enqueue, por lo que itera `n` veces (tamaño total) y detiene a 1, equivalente a rotar `n-1` veces los elementos anteriores. `top()` accede directamente a `this.queue[0]`, el índice de la cabeza del array.

- **PHP:** Se utiliza `SplQueue`, la estructura de datos de la SPL (Standard PHP Library) diseñada específicamente para semántica FIFO, en lugar de un array genérico. Esto hace el contrato de queue explícito y robusto. Un detalle arquitectónico notable es que el `push` captura el tamaño **antes** del `enqueue` (`$size = $this->queue->count()`), por lo que el bucle itera exactamente `$size` veces (los elementos previos), lo cual es semánticamente equivalente pero más preciso que la versión JS. Para `top()`, `SplQueue` expone el método `->bottom()`, que devuelve el elemento en la cabeza de la queue (el primero enqueued que aún no ha sido dequeued), accesible en `O(1)`.

> [!IMPORTANT]
> La diferencia de `bottom()` en PHP merece atención: `SplDoublyLinkedList` (del que hereda `SplQueue`) considera `top()` el último elemento insertado (tail) y `bottom()` el primero (head/front). En este contexto, el "fondo" de la SplQueue es la cabeza semántica del stack simulado tras las rotaciones.

---

## Lecciones Clave

- **Patrón de Simulación de ADT (Abstract Data Type):** Este problema ilustra cómo cualquier estructura de datos puede ser emulada a partir de primitivas más básicas pagando un costo asimétrico en alguna de sus operaciones. Aplicar este patrón cuando se trabaja en entornos restringidos (ej. sistemas embebidos, APIs con acceso limitado a estructuras, o problemas de diseño de sistemas donde se simula una `Priority Queue` con un `Heap` manual).

- **Trade-off Push-Costoso vs Pop-Costoso:** Existen dos variantes simétricas para este problema. La elegida aquí (`O(n)` push / `O(1)` pop) es óptima cuando las lecturas y extracciones son más frecuentes que las inserciones. Si el patrón de uso fuera el inverso, la variante `O(1)` push / `O(n)` pop sería preferible. Siempre analizar el **perfil de acceso** (access pattern) antes de elegir la variante a implementar.
