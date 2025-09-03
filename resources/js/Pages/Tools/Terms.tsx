import Layout from "@/Layouts/Layout";
import { Head } from "@inertiajs/react";

export default function Terms() {
  return (
  
      <>
        <Head title="Términos y Condiciones - AESTHETIC" />
        
        <div className="max-w-4xl mx-auto py-8">
          {/* Header */}
          <div className="text-center mb-12">
            <h1 className="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">
              Términos y Condiciones del Servicio
            </h1>
            <p className="text-lg text-gray-600 dark:text-gray-300">
              Última actualización: 2 de Julio de 2025
            </p>
          </div>

          {/* Content */}
          <div className="bg-white dark:bg-neutral-800 rounded-xl shadow-lg p-8 space-y-8">
            
            {/* Sección 1 */}
            <section>
              <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                1. Aceptación de los Términos
              </h2>
              <p className="text-gray-600 dark:text-gray-300 leading-relaxed mb-4">
                Al acceder y utilizar los servicios de AESTHETIC, usted acepta estar sujeto a estos 
                términos y condiciones de uso. Si no está de acuerdo con alguna parte de estos términos, 
                no debe utilizar nuestros servicios.
              </p>
              <p className="text-gray-600 dark:text-gray-300 leading-relaxed">
                Estos términos se aplican a todos los usuarios del sitio web, incluidos, entre otros, 
                usuarios que son navegadores, pacientes, comerciantes, proveedores y/o contribuyentes de contenido.
              </p>
            </section>

            {/* Sección 2 */}
            <section>
              <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                2. Servicios Médicos y Estéticos
              </h2>
              <div className="space-y-4">
                <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
                  2.1 Naturaleza de los Servicios
                </h3>
                <p className="text-gray-600 dark:text-gray-300 leading-relaxed">
                  AESTHETIC ofrece servicios de medicina estética realizados por profesionales 
                  médicos calificados y licenciados. Todos los tratamientos requieren evaluación 
                  médica previa y consentimiento informado.
                </p>
                
                <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
                  2.2 Requisitos del Paciente
                </h3>
                <ul className="list-disc list-inside text-gray-600 dark:text-gray-300 space-y-2">
                  <li>Ser mayor de 18 años o contar con autorización de padres/tutores</li>
                  <li>Proporcionar información médica completa y veraz</li>
                  <li>Seguir las instrucciones pre y post-tratamiento</li>
                  <li>Informar sobre alergias, medicamentos y condiciones médicas</li>
                </ul>
              </div>
            </section>

            {/* Sección 3 */}
            <section>
              <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                3. Reservas y Citas
              </h2>
              <div className="space-y-4">
                <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
                  3.1 Proceso de Reserva
                </h3>
                <p className="text-gray-600 dark:text-gray-300 leading-relaxed">
                  Las citas pueden ser programadas a través de nuestro sitio web, teléfono o WhatsApp. 
                  La confirmación de la cita está sujeta a disponibilidad y verificación de datos.
                </p>
                
                <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
                  3.2 Política de Cancelación
                </h3>
                <ul className="list-disc list-inside text-gray-600 dark:text-gray-300 space-y-2">
                  <li>Cancelaciones con más de 24 horas de anticipación: Sin penalización</li>
                  <li>Cancelaciones entre 12-24 horas: 50% del valor del tratamiento</li>
                  <li>Cancelaciones con menos de 12 horas: 100% del valor del tratamiento</li>
                  <li>No presentarse a la cita: 100% del valor del tratamiento</li>
                </ul>
              </div>
            </section>

            {/* Sección 4 */}
            <section>
              <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                4. Precios y Pagos
              </h2>
              <div className="space-y-4">
                <p className="text-gray-600 dark:text-gray-300 leading-relaxed">
                  Los precios están sujetos a cambio sin previo aviso. Se aceptan las siguientes 
                  formas de pago: efectivo, tarjetas de crédito, débito y transferencias bancarias.
                </p>
                <p className="text-gray-600 dark:text-gray-300 leading-relaxed">
                  Para tratamientos de alto valor, se puede solicitar un pago anticipado o depósito. 
                  Los pagos no son reembolsables excepto en circunstancias médicas que impidan 
                  realizar el tratamiento.
                </p>
              </div>
            </section>

            {/* Sección 5 */}
            <section>
              <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                5. Riesgos y Limitaciones
              </h2>
              <div className="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg">
                <p className="text-gray-700 dark:text-gray-300 leading-relaxed">
                  <strong>IMPORTANTE:</strong> Todos los procedimientos médicos conllevan riesgos inherentes. 
                  Los resultados pueden variar entre pacientes y no se garantizan resultados específicos. 
                  Es fundamental seguir todas las instrucciones médicas para minimizar riesgos y optimizar resultados.
                </p>
              </div>
            </section>

            {/* Sección 6 */}
            <section>
              <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                6. Privacidad y Confidencialidad
              </h2>
              <p className="text-gray-600 dark:text-gray-300 leading-relaxed">
                Toda la información personal y médica se maneja bajo estricta confidencialidad según 
                las leyes colombianas de protección de datos (Ley 1581 de 2012). Los datos solo se 
                utilizarán para fines médicos y administrativos relacionados con el tratamiento.
              </p>
            </section>

            {/* Sección 7 */}
            <section>
              <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                7. Uso del Sitio Web
              </h2>
              <div className="space-y-4">
                <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
                  Está prohibido:
                </h3>
                <ul className="list-disc list-inside text-gray-600 dark:text-gray-300 space-y-2">
                  <li>Usar el sitio para fines ilegales o no autorizados</li>
                  <li>Intentar acceder a áreas restringidas del sistema</li>
                  <li>Transmitir virus o código malicioso</li>
                  <li>Reproducir contenido sin autorización</li>
                  <li>Hacer un uso excesivo que afecte el rendimiento del sitio</li>
                </ul>
              </div>
            </section>

            {/* Sección 8 */}
            <section>
              <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                8. Propiedad Intelectual
              </h2>
              <p className="text-gray-600 dark:text-gray-300 leading-relaxed">
                Todo el contenido del sitio web, incluyendo textos, imágenes, logos, diseños y software, 
                está protegido por derechos de autor y otras leyes de propiedad intelectual. 
                No está permitida la reproducción sin autorización expresa.
              </p>
            </section>

            {/* Sección 9 */}
            <section>
              <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                9. Limitación de Responsabilidad
              </h2>
              <p className="text-gray-600 dark:text-gray-300 leading-relaxed">
                AESTHETIC no será responsable por daños indirectos, incidentales, especiales o 
                consecuentes que resulten del uso de nuestros servicios, excepto en casos de 
                negligencia grave o dolo, según lo establecido en la legislación colombiana.
              </p>
            </section>

            {/* Sección 10 */}
            <section>
              <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                10. Modificaciones
              </h2>
              <p className="text-gray-600 dark:text-gray-300 leading-relaxed">
                AESTHETIC se reserva el derecho de modificar estos términos en cualquier momento. 
                Las modificaciones entrarán en vigor inmediatamente después de su publicación en 
                el sitio web. Es responsabilidad del usuario revisar periódicamente estos términos.
              </p>
            </section>

            {/* Sección 11 */}
            <section>
              <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                11. Ley Aplicable y Jurisdicción
              </h2>
              <p className="text-gray-600 dark:text-gray-300 leading-relaxed">
                Estos términos se rigen por las leyes de la República de Colombia. Cualquier disputa 
                será resuelta en los tribunales competentes de Bogotá, Colombia.
              </p>
            </section>

            {/* Sección 12 */}
            <section>
              <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                12. Contacto
              </h2>
              <div className="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                <p className="text-gray-700 dark:text-gray-300 leading-relaxed mb-2">
                  Para preguntas sobre estos términos y condiciones, puede contactarnos:
                </p>
                <ul className="text-gray-600 dark:text-gray-300 space-y-1">
                  <li><strong>Email:</strong> legal@aesthetic.com</li>
                  <li><strong>Teléfono:</strong> +57 321 770 6324</li>
                  <li><strong>WhatsApp:</strong> +57 321 770 6324</li>
                  <li><strong>Dirección:</strong> Bogotá, Colombia</li>
                </ul>
              </div>
            </section>

            {/* Footer */}
            <div className="border-t border-gray-200 dark:border-gray-600 pt-6 text-center">
              <p className="text-sm text-gray-500 dark:text-gray-400">
                Al utilizar nuestros servicios, usted confirma que ha leído, entendido y acepta 
                estos términos y condiciones en su totalidad.
              </p>
            </div>
          </div>
        </div>
      </>

  );
}