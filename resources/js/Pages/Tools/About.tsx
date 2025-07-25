import Layout from "@/Layouts/Layout";
import { Head } from "@inertiajs/react";

export default function About({ team, values }) {
  return (
    <>
      <Head title="Quiénes Somos - AESTHECTIC" />

      <Layout>
        <div className="max-w-6xl mx-auto py-8">
          {/* Hero Section */}
          <div className="text-center mb-16">
            <h1 className="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-6">
              Quiénes Somos
            </h1>
            <p className="text-xl text-gray-600 dark:text-gray-300 max-w-4xl mx-auto leading-relaxed">
              En AESTHECTIC, somos pioneros en medicina estética, dedicados a realzar tu belleza natural
              con los más altos estándares de calidad y profesionalismo.
            </p>
          </div>

          {/* Mission & Vision */}
          <div className="grid md:grid-cols-2 gap-8 mb-16">
            <div className="bg-white dark:bg-neutral-800 p-8 rounded-xl shadow-lg">
              <h2 className="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                Nuestra Misión
              </h2>
              <p className="text-gray-600 dark:text-gray-300 leading-relaxed">
                Brindar servicios de medicina estética de la más alta calidad, utilizando tecnología
                de vanguardia y técnicas innovadoras para ayudar a nuestros pacientes a sentirse
                seguros y satisfechos con su apariencia, siempre priorizando su seguridad y bienestar.
              </p>
            </div>

            <div className="bg-white dark:bg-neutral-800 p-8 rounded-xl shadow-lg">
              <h2 className="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                Nuestra Visión
              </h2>
              <p className="text-gray-600 dark:text-gray-300 leading-relaxed">
                Ser el centro de medicina estética de referencia, reconocido por nuestra excelencia,
                innovación y compromiso con la satisfacción del paciente, contribuyendo al bienestar
                y autoestima de nuestra comunidad.
              </p>
            </div>
          </div>

          {/* Values */}
          <div className="mb-16">
            <h2 className="text-3xl font-bold text-center text-gray-900 dark:text-white mb-12">
              Nuestros Valores
            </h2>
            <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
              {values.map((value, index) => (
                <div key={index} className="bg-white dark:bg-neutral-800 p-6 rounded-xl shadow-lg text-center hover:shadow-xl transition-shadow">
                  <div className="text-4xl mb-4">{value.icon}</div>
                  <h3 className="text-xl font-bold text-gray-900 dark:text-white mb-3">
                    {value.title}
                  </h3>
                  <p className="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">
                    {value.description}
                  </p>
                </div>
              ))}
            </div>
          </div>

          {/* Team */}
          <div className="mb-16">
            <h2 className="text-3xl font-bold text-center text-gray-900 dark:text-white mb-12">
              Nuestro Equipo
            </h2>
            <div className="grid md:grid-cols-3 gap-8">
              {team.map((member, index) => (
                <div key={index} className="bg-white dark:bg-neutral-800 rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                  <img
                    src={member.image}
                    alt={member.name}
                    className="w-full h-64 object-cover"
                  />
                  <div className="p-6">
                    <h3 className="text-xl font-bold text-gray-900 dark:text-white mb-2 flex items-center gap-2">
                      {member.name}
                      {typeof member.average_rating === 'number' && (
                        <span className="flex items-center text-yellow-500 text-base font-normal ml-2" title={`Calificación promedio: ${member.average_rating}`}>
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" className="w-5 h-5 mr-0.5">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.18c.969 0 1.371 1.24.588 1.81l-3.385 2.46a1 1 0 00-.364 1.118l1.286 3.967c.3.921-.755 1.688-1.54 1.118l-3.385-2.46a1 1 0 00-1.175 0l-3.385 2.46c-.784.57-1.838-.197-1.54-1.118l1.286-3.967a1 1 0 00-.364-1.118l-3.385-2.46c-.783-.57-.38-1.81.588-1.81h4.18a1 1 0 00.95-.69l1.286-3.967z" />
                          </svg>
                          {member.average_rating}
                          {member.ratings_count > 0}
                        </span>
                      )}
                    </h3>
                    <p className="text-blue-600 font-medium mb-1">
                      {member.role}
                    </p>
                    <p className="text-gray-600 dark:text-gray-300 text-sm mb-2">
                      {member.experience}
                    </p>
                    {member.services && member.services.length > 0 && (
                      <div className="text-gray-600 dark:text-gray-300 text-sm">
                        <p className="font-medium">Servicios:</p>
                        <ul className="list-disc pl-5">
                          {member.services.map((service, idx) => (
                            <li key={idx}>{service}</li>
                          ))}
                        </ul>
                      </div>
                    )}
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* History */}
          <div className="bg-gray-50 dark:bg-neutral-800 rounded-xl p-8 mb-16">
            <h2 className="text-3xl font-bold text-gray-900 dark:text-white mb-6 text-center">
              Nuestra Historia
            </h2>
            <div className="max-w-4xl mx-auto">
              <p className="text-gray-600 dark:text-gray-300 leading-relaxed mb-4">
                AESTHECTIC nació en 2015 con la visión de revolucionar el mundo de la medicina estética
                en Colombia. Fundada por un equipo de profesionales médicos especializados, nuestra clínica
                ha crecido para convertirse en un referente en tratamientos estéticos de alta calidad.
              </p>
              <p className="text-gray-600 dark:text-gray-300 leading-relaxed mb-4">
                A lo largo de estos años, hemos tratado a más de 5,000 pacientes satisfechos, manteniendo
                siempre nuestro compromiso con la excelencia y la innovación. Nuestro equipo se mantiene
                en constante formación para ofrecer las técnicas más avanzadas y seguras del mercado.
              </p>
              <p className="text-gray-600 dark:text-gray-300 leading-relaxed">
                Hoy en día, AESTHECTIC continúa expandiéndose y mejorando, siempre con el objetivo
                de brindar a nuestros pacientes la mejor experiencia en medicina estética.
              </p>
            </div>
          </div>

          {/* Certifications */}
          <div className="text-center">
            <h2 className="text-3xl font-bold text-gray-900 dark:text-white mb-8">
              Certificaciones y Reconocimientos
            </h2>
            <div className="grid grid-cols-2 md:grid-cols-4 gap-6">
              <div className="bg-white dark:bg-neutral-800 p-4 rounded-lg shadow-lg">
                <div className="text-2xl mb-2">🏆</div>
                <p className="text-sm font-medium text-gray-900 dark:text-white">
                  Certificación ISO 9001
                </p>
              </div>
              <div className="bg-white dark:bg-neutral-800 p-4 rounded-lg shadow-lg">
                <div className="text-2xl mb-2">🏥</div>
                <p className="text-sm font-medium text-gray-900 dark:text-white">
                  Habilitación INVIMA
                </p>
              </div>
              <div className="bg-white dark:bg-neutral-800 p-4 rounded-lg shadow-lg">
                <div className="text-2xl mb-2">👨‍⚕️</div>
                <p className="text-sm font-medium text-gray-900 dark:text-white">
                  Sociedad Colombiana de Medicina Estética
                </p>
              </div>
              <div className="bg-white dark:bg-neutral-800 p-4 rounded-lg shadow-lg">
                <div className="text-2xl mb-2">⭐</div>
                <p className="text-sm font-medium text-gray-900 dark:text-white">
                  Premio Excelencia 2024
                </p>
              </div>
            </div>
          </div>
        </div>
      </Layout>
    </>
  );
}
