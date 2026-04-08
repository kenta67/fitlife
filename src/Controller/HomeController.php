<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $planes = [
            [
                'nombre'    => 'Básico',
                'precio'    => '29',
                'duracion'  => '1 mes',
                'features'  => ['Acceso sala principal', 'Vestuarios', 'Casillero'],
                'destacado' => false,
            ],
            [
                'nombre'    => 'Básico 3 meses',
                'precio'    => '79',
                'duracion'  => '3 meses',
                'features'  => ['Acceso sala principal', 'Vestuarios', 'Casillero', 'Ahorro 10%'],
                'destacado' => false,
            ],
            [
                'nombre'    => 'Básico 1 año',
                'precio'    => '299',
                'duracion'  => '1 año',
                'features'  => ['Acceso sala principal', 'Vestuarios', 'Casillero', 'Ahorro 20%', '2 meses gratis'],
                'destacado' => false,
            ],
            [
                'nombre'    => 'Pro',
                'precio'    => '59',
                'duracion'  => '1 mes',
                'features'  => ['Todo Básico', 'Clases grupales', 'Nutricionista', 'App de seguimiento'],
                'destacado' => true,
            ],
            [
                'nombre'    => 'Pro 3 meses',
                'precio'    => '149',
                'duracion'  => '3 meses',
                'features'  => ['Todo Básico', 'Clases grupales', 'Nutricionista', 'App de seguimiento', 'Ahorro 15%'],
                'destacado' => false,
            ],
            [
                'nombre'    => 'Pro 1 año',
                'precio'    => '549',
                'duracion'  => '1 año',
                'features'  => ['Todo Básico', 'Clases grupales', 'Nutricionista', 'App de seguimiento', 'Ahorro 25%', '3 meses gratis'],
                'destacado' => false,
            ],
        ];

        $clases = $this->getClasesData();

        // Vista previa de 4 máquinas para el home
        $maquinasPreview = [
            ['nombre' => 'Cinta de Correr',  'icono' => '🏃', 'descripcion' => 'Cardiovascular ideal para correr o caminar con intensidad ajustable.'],
            ['nombre' => 'Press de Banca',   'icono' => '💪', 'descripcion' => 'Fundamental para desarrollar pectorales, hombros y tríceps.'],
            ['nombre' => 'Polea Multi',      'icono' => '🏋️', 'descripcion' => 'Versátil para espalda, hombros y brazos en un solo equipo.'],
            ['nombre' => 'Máquina de Remo',  'icono' => '🚣', 'descripcion' => 'Cardio de cuerpo completo que mejora postura y resistencia.'],
        ];

        return $this->render('home/index.html.twig', [
            'planes'    => $planes,
            'clases'    => $clases,
            'maquinas'  => $maquinasPreview,
        ]);
    }

    #[Route('/ubicacion', name: 'app_ubicacion')]
    public function ubicacion(): Response
    {
        return $this->render('home/ubicacion.html.twig');
    }

    #[Route('/clases/{slug}', name: 'app_clase_detalle', requirements: ['slug' => 'yoga|crossfit|spinning|boxeo|pilates|zumba'])]
    public function claseDetalle(string $slug): Response
    {
        $clases = $this->getClasesData();

        $claseActual = null;
        foreach ($clases as $clase) {
            if ($clase['slug'] === $slug) {
                $claseActual = $clase;
                break;
            }
        }

        if (!$claseActual) {
            throw $this->createNotFoundException('Clase no encontrada');
        }

        $otrasClases = array_filter($clases, fn($c) => $c['slug'] !== $slug);

        return $this->render('home/clase_detalle.html.twig', [
            'clase'       => $claseActual,
            'otrasClases' => array_values($otrasClases),
        ]);
    }

    #[Route('/maquinas', name: 'app_maquinas')]
    public function maquinas(): Response
    {
        return $this->render('home/maquinas.html.twig', [
            'maquinas' => $this->getMaquinasData(),
        ]);
    }

    #[Route('/contacto', name: 'app_contacto')]
    public function contacto(): Response
    {
        return $this->redirectToRoute('app_ubicacion');
    }

    /* ================================================================
       DATOS ESTÁTICOS (en producción podrían venir de base de datos)
       ================================================================ */

    private function getClasesData(): array
    {
        return [
            [
                'slug'       => 'yoga',
                'nombre'     => 'Yoga',
                'horario'    => 'Mar – Jue 07:00',
                'icono'      => '🧘',
                'gradiente'  => 'linear-gradient(135deg, #0f0c29 0%, #1a1a40 40%, #24243e 100%)',
                'que_es'     => 'Disciplina milenaria originaria de la India que une cuerpo, mente y espíritu a través de posturas físicas (asanas), técnicas de respiración controlada (pranayama) y meditación. En FitLife ofrecemos Yoga Hatha y Vinyasa adaptados a todos los niveles.',
                'actividades'=> 'Saludo al sol, posturas de equilibrio (árbol, guerrero), flexiones hacia adelante, torsiones espinales, ejercicios de respiración diafragmática, relajación profunda (savasana) y meditación guiada.',
                'beneficios' => 'Aumenta la flexibilidad y movilidad articular, reduce los niveles de estrés y ansiedad, mejora la postura corporal, fortalece el core profundo, favorece la concentración y claridad mental, mejora la calidad del sueño.',
            ],
            [
                'slug'       => 'crossfit',
                'nombre'     => 'CrossFit',
                'horario'    => 'Lun – Mié – Vie 06:00',
                'icono'      => '🔥',
                'gradiente'  => 'linear-gradient(135deg, #1a0000 0%, #3d0c0c 40%, #1a0a00 100%)',
                'que_es'     => 'Programa de entrenamiento de fuerza y acondicionamiento de alta intensidad basado en movimientos funcionales constantemente variados. Cada sesión presenta un WOD (Workout of the Day) diferente que desafía tu cuerpo de formas nuevas.',
                'actividades'=> 'Levantamiento olímpico (clean & jerk, snatch), gimnasia (dominadas, muscle-ups, handstands), ejercicios metabólicos (burpees, box jumps, double-unders), carreras cortas, tracción con cuerdas y trabajo con kettlebells.',
                'beneficios' => 'Aumento significativo de potencia y fuerza muscular, quema calórica acelerada (hasta 800 kcal por sesión), mejora la resistencia cardiovascular, fomenta el trabajo en equipo y la superación personal, desarrolla capacidad funcional para la vida diaria.',
            ],
            [
                'slug'       => 'spinning',
                'nombre'     => 'Spinning',
                'horario'    => 'Lun – Mié – Vie 18:00',
                'icono'      => '🚴',
                'gradiente'  => 'linear-gradient(135deg, #001a0a 0%, #0a2a1a 40%, #001a0a 100%)',
                'que_es'     => 'Clase de ciclismo indoor guiada por instructores certificados, con música motivadora y simulación de diferentes terrenos y ritmos. Cada sesión es una experiencia inmersiva que combina velocidad, resistencia y diversión.',
                'actividades'=> 'Calentamiento progresivo, sprints de alta intensidad, simulación de subidas con resistencia, pedaleo en posición sentada y de pie, intervalos de recuperación activa, técnica de cadencia y enfriamiento guiado con estiramientos.',
                'beneficios' => 'Mejora drástica de la resistencia cardiovascular, tonificación de cuádriceps, isquiotibiales y glúteos, quema calórica intensa (500-700 kcal), impacto cero en articulaciones, libera endorfinas y mejora el estado de ánimo.',
            ],
            [
                'slug'       => 'boxeo',
                'nombre'     => 'Boxeo',
                'horario'    => 'Mar – Jue – Sáb 19:00',
                'icono'      => '🥊',
                'gradiente'  => 'linear-gradient(135deg, #0a0a0a 0%, #2a1010 40%, #0a0a0a 100%)',
                'que_es'     => 'Entrenamiento de boxeo que combina técnica de combate con ejercicios físicos de alta intensidad. No es necesario tener experiencia previa: nuestras clases están diseñadas para todos los niveles, desde principiantes hasta avanzados.',
                'actividades'=> 'Shadow boxing (técnica frente al espejo), golpeo de saco pesado y saco de velocidad, trabajo de pies y desplazamiento, combinaciones de golpes (jab, cross, hook, uppercut), saltos de cuerda, ejercicios funcionales de core y burpees.',
                'beneficios' => 'Desarrolla coordinación mano-ojo excepcional, aumenta la velocidad de reacción, fortalece la potencia explosiva del tren superior, libera tensión y estrés de forma efectiva, enseña fundamentos de defensa personal, incrementa la confianza en uno mismo.',
            ],
            [
                'slug'       => 'pilates',
                'nombre'     => 'Pilates',
                'horario'    => 'Mar – Jue 08:00',
                'icono'      => '🌿',
                'gradiente'  => 'linear-gradient(135deg, #0a1a0a 0%, #1a2a1a 40%, #0a1a10 100%)',
                'que_es'     => 'Método de acondicionamiento físico creado por Joseph Pilates que se centra en el fortalecimiento del centro del cuerpo (core) a través de movimientos controlados, precisos y fluidos. Trabajamos tanto en suelo como con accesorios.',
                'actividades'=> 'Ejercicios de suelo (the hundred, roll-up, single leg stretch), trabajo con banda elástica y balón suizo, ejercicios de respiración costal, fortalecimiento del suelo pélvico, estiramientos controlados y ejercicios de equilibrio.',
                'beneficios' => 'Mejora notable de la postura corporal, alivio de dolores lumbares y cervicales, aumenta la flexibilidad sin riesgo de lesión, tonifica la musculatura profunda sin ganar volumen excesivo, desarrolla una mayor conciencia corporal y conexión mente-cuerpo.',
            ],
            [
                'slug'       => 'zumba',
                'nombre'     => 'Zumba',
                'horario'    => 'Sáb – Dom 09:00',
                'icono'      => '💃',
                'gradiente'  => 'linear-gradient(135deg, #1a0a1a 0%, #2a0a2a 40%, #1a0a20 100%)',
                'que_es'     => 'Programa de fitness dance que combina movimientos de baile con ejercicios aeróbicos al ritmo de música latina y mundial. Las coreografías son divertidas, fáciles de seguir y están diseñadas para que te muevas sin darte cuenta de que estás entrenando.',
                'actividades'=> 'Coreografías de salsa, merengue, reggaetón, cumbia, bachata y hip-hop, calentamiento con movimientos suaves, bloques de alta intensidad con ritmos rápidos, secciones de enfriamiento, estiramientos finales al ritmo de música suave.',
                'beneficios' => 'Quema entre 400 y 600 calorías por sesión sin sentir que es un esfuerzo, mejora drásticamente el estado de ánimo y reduce el estrés, desarrolla coordinación rítmica y memoria motriz, fomenta la socialización, garantiza diversión asegurada en cada clase.',
            ],
        ];
    }

    private function getMaquinasData(): array
    {
        return [
            [
                'nombre'     => 'Cinta de Correr',
                'icono'      => '🏃',
                'categoria'  => 'cardio',
                'descripcion'=> 'Equipo cardiovascular de última generación con pantalla interactiva, múltiples programas de entrenamiento y sistema de amortiguación que protege las articulaciones.',
                'para_que'   => 'Ideal para entrenamientos de cardio continuo, intervalos de alta intensidad (HIIT), calentamiento previo al entrenamiento de fuerza, rehabilitación de lesiones de miembros inferiores y caminatas activas.',
                'beneficios' => 'Mejora la resistencia cardiovascular y la capacidad pulmonar, quema entre 300 y 600 calorías por sesión, permite ajustar velocidad e inclinación según el nivel, fortalece cuádriceps, isquiotibiales y glúteos de forma progresiva.',
            ],
            [
                'nombre'     => 'Bicicleta Estática',
                'icono'      => '🚲',
                'categoria'  => 'cardio',
                'descripcion'=> 'Bicicleta de spinning profesional con resistencia magnética ajustable, sillín ergonómico y pedales con clip para un pedaleo eficiente y seguro.',
                'para_que'   => 'Perfecta para sesiones de cardio de bajo impacto, entrenamiento de resistencia en piernas, recuperación activa post-entrenamiento, calentamiento y trabajo de zona de quema grasa.',
                'beneficios' => 'Fortalece cuádriceps, isquiotibiales y glúteos, mejora la capacidad pulmonar y la resistencia vascular, cero impacto en rodillas y tobillos, permite entrenar escuchando música o viendo contenido simultáneamente.',
            ],
            [
                'nombre'     => 'Escaladora Elíptica',
                'icono'      => '⛰️',
                'categoria'  => 'cardio',
                'descripcion'=> 'Equipo de cardio de movimiento fluido que simula la combinación de caminata, escalada y esquí. Incluye manillares móviles para trabajo de tren superior.',
                'para_que'   => 'Indicada para entrenamientos cardiovasculares completos, rehabilitación articular, trabajo de zona aeróbica y sesiones de baja intensidad de larga duración.',
                'beneficios' => 'Trabaja piernas, glúteos, brazos y core simultáneamente, impacto prácticamente nulo en articulaciones, quema calórica eficiente (400-500 kcal/hora), mejora la coordinación y el equilibrio corporal.',
            ],
            [
                'nombre'     => 'Máquina de Remo',
                'icono'      => '🚣',
                'categoria'  => 'cardio',
                'descripcion'=> 'Remo profesional con resistencia de agua o aire que simula la sensación real de remar. Monitor que muestra tiempo, distancia, calorías y ritmo por minuto.',
                'para_que'   => 'Excelente para entrenamiento cardiovascular de cuerpo completo, calentamiento general, trabajo de zona anaeróbica en intervalos cortos y desarrollo de potencia muscular en tiradas largas.',
                'beneficios' => 'Trabaja hasta el 84% de la musculatura corporal en cada tirada, fortalece dorsales, bíceps, core y piernas, mejora drásticamente la postura de espalda, previene lesiones dorsales, bajo impacto articular.',
            ],
            [
                'nombre'     => 'Press de Banca',
                'icono'      => '💪',
                'categoria'  => 'fuerza',
                'descripcion'=> 'Estructura robusta con banco ajustable en múltiples ángulos (plano, inclinado, declinado) y soportes de seguridad para entrenar con total confianza.',
                'para_que'   => 'Fundamental para el desarrollo del tren superior: pectorales, deltoides anteriores y tríceps. Permite trabajar con barra olímpica y mancuernas en diferentes ángulos.',
                'beneficios' => 'Desarrolla masa muscular en pectoral mayor y menor, aumenta la fuerza de empuje horizontal, mejora la estabilidad del hombro, permite progresión de carga segura con los soportes de seguridad integrados.',
            ],
            [
                'nombre'     => 'Prensa de Piernas',
                'icono'      => '🦵',
                'categoria'  => 'fuerza',
                'descripcion'=> 'Máquina de gran capacidad con respaldo ajustable a 45°, plataforma ancha anti-deslizante y sistema de carga por discos olímpicos con guías laterales.',
                'para_que'   => 'Diseñada para el fortalecimiento intenso del tren inferior: cuádriceps, isquiotibiales y glúteos. Permite trabajar con cargas superiores a las del squat sin comprometer la espalda baja.',
                'beneficios' => 'Aísla y fortalece cuádriceps de forma segura, permite cargar más peso que en sentadilla libre, protege la columna lumbar gracias al respaldo, ideal para ganancia de fuerza y masa muscular en piernas.',
            ],
            [
                'nombre'     => 'Polea Multiposición',
                'icono'      => '🔗',
                'categoria'  => 'fuerza',
                'descripcion'=> 'Sistema de poleas ajustables en múltiples alturas con haz doble, discos de carga integrados y amplio surtido de agarres y accesorios intercambiables.',
                'para_que'   => 'Equipo más versátil del gimnasio: permite ejercicios de tracción (jalón al pecho, remo), empuje (press de pecho, extensiones), rotaciones y ejercicios funcionales.',
                'beneficios' => 'Sustituye decenas de máquinas individuales, permite entrenar todos los grupos musculares, ofrece resistencia constante durante todo el rango de movimiento, ideal para circuitos de entrenamiento funcional.',
            ],
            [
                'nombre'     => 'Smith Machine',
                'icono'      => '🏗️',
                'categoria'  => 'fuerza',
                'descripcion'=> 'Estructura de acero con barra guiada verticalmente, sistema de seguro a múltiples alturas y opciones de banco desmontable para variedad de ejercicios.',
                'para_que'   => 'Permite realizar sentadillas, press de banca, press militar, peso muerto rumano y decenas de ejercicios con la seguridad de la barra guiada que evita caídas accidentales.',
                'beneficios' => 'Entrena de forma segura sin necesitar compañero de entrenamiento (spotter), barra guiada facilita la técnica correcta, permite aislar grupos musculares específicos, versatilidad máxima en un solo equipo.',
            ],
            [
                'nombre'     => 'Máquina de Dorsales',
                'icono'      => '🔻',
                'categoria'  => 'fuerza',
                'descripcion'=> 'Equipo de jalón vertical con rodillera ajustable, barra amplia y agarres neutros, diseñado para el desarrollo completo de la musculatura de la espalda.',
                'para_que'   => 'Especializada en el fortalecimiento del dorsal ancho (latissimus dorsi), romboides y bíceps. Ejercicio clave para construir una espalda ancha y definida.',
                'beneficios' => 'Desarrolla la anchura de la espalda de forma eficaz, fortalece el agarre y los bíceps como músculos sinérgicos, mejora la postura al corregir hombros caídos, permite progresión de carga controlada.',
            ],
            [
                'nombre'     => 'Leg Curl',
                'icono'      => '🔄',
                'categoria'  => 'fuerza',
                'descripcion'=> 'Máquina de aislamiento para isquiotibiales con rodillo acolchado ajustable, respaldo inclinado y sistema de carga por pin o discos según el modelo.',
                'para_que'   => 'Aísla los isquiotibiales (parte posterior del muslo) para su fortalecimiento específico. Complemento indispensable del trabajo de cuádriceps en la prensa de piernas.',
                'beneficios' => 'Previene lesiones de rodilla al equilibrar la fuerza entre cuádriceps e isquiotibiales, mejora la definición de la parte posterior del muslo, ideal para rehabilitación de desgarros musculares, bajo riesgo de lesión por su diseño guiado.',
            ],
            [
                'nombre'     => 'Máquina Abdominal',
                'icono'      => '🎯',
                'categoria'  => 'fuerza',
                'descripcion'=> 'Equipo con cojines ergonómicos, respaldo ajustable y sistema de resistencia progresiva para trabajar la musculatura abdominal desde múltiples ángulos.',
                'para_que'   => 'Fortalece el recto abdominal, oblicuos y la faja abdominal profunda de forma segura, evitando la tensión cervical que se produce en los crunches en el suelo.',
                'beneficios' => 'Fortalece el core de forma progresiva y segura, reduce la tensión en cuello y espalda baja respecto a ejercicios en suelo, permite añadir carga gradualmente, mejora la estabilidad del tronco para todos los demás ejercicios.',
            ],
            [
                'nombre'     => 'Mancuernas Ajustables',
                'icono'      => '⚡',
                'categoria'  => 'libre',
                'descripcion'=> 'Set completo de mancuernas ajustables desde 2 kg hasta 40 kg, con sistema de cambio rápido de platos y racks de almacenamiento organizados por peso.',
                'para_que'   => 'Libertad total de movimiento para ejercicios de brazos, hombros, pecho, espalda y piernas. Permiten patrones de movimiento naturales que las máquinas no pueden replicar.',
                'beneficios' => 'Desarrollan estabilidad articular y músculos estabilizadores, permiten un rango de movimiento completo y natural, progresión de carga flexible, ideales para entrenamiento unilateral que corrige desequilibrios musculares.',
            ],
        ];
    }
}