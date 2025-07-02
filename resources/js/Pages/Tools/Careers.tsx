import Layout from "@/Layouts/Layout";
import { Head } from "@inertiajs/react";
import { useState } from "react";

export default function Careers() {
  const [selectedJob, setSelectedJob] = useState<number | null>(null);

  const jobOpenings = [
    {
      id: 1,
      title: "Médico Especialista en Medicina Estética",
      department: "Área Médica",
      location: "Bogotá, Colombia",
      type: "Tiempo Completo",
      experience: "3+ años",
      salary: "$8,000,000 - $12,000,000 COP",
      description: "Buscamos un médico especialista para unirse a nuestro equipo de profesionales en medicina estética.",
      requirements: [
        "Título de Médico General con especialización en Medicina Estética",
        "Mínimo 3 años de experiencia en procedimientos estéticos",
        "Registro médico vigente (RETHUS)",
        "Experiencia en toxina botulínica, rellenos dérmicos",
        "Excelentes habilidades de comunicación con pacientes"
      ],
      benefits: [
        "Salario competitivo",
        "Comisiones por productividad",
        "Capacitación continua",
        "Ambiente de trabajo profesional",
        "Oportunidades de crecimiento"
      ]
    },
    {
      id: 2,
      title: "Enfermero/a Profesional",
      department: "Área de Enfermería",
      location: "Bogotá, Colombia",
      type: "Tiempo Completo",
      experience: "2+ años",
      salary: "$2,500,000 - $3,500,000 COP",
      description: "Enfermero profesional para apoyar procedimientos estéticos y cuidado pre/post operatorio.",
      requirements: [
        "Título de Enfermería Profesional",
        "Mínimo 2 años de experiencia",
        "Registro profesional vigente",
        "Experiencia en procedimientos menores",
        "Conocimiento en primeros auxilios"
      ],
      benefits: [
        "Prestaciones de ley",
        "Bonificaciones por desempeño",
        "Capacitación especializada",
        "Horarios flexibles",
        "Descuentos en tratamientos"
      ]
    },
    {
      id: 3,
      title: "Recepcionista y Atención al Cliente",
      department: "Servicio al Cliente",
      location: "Bogotá, Colombia",
      type: "Tiempo Completo",
      experience: "1+ años",
      salary: "$1,500,000 - $2,200,000 COP",
      description: "Recepcionista para primera atención a clientes, agenda de citas y soporte administrativo.",
      requirements: [
        "Bachiller académico completo",
        "Experiencia en atención al cliente",
        "Manejo de sistemas de información",
        "Excelente presentación personal",
        "Habilidades de comunicación"
      ],
      benefits: [
        "Prestaciones de ley",
        "Bonos por metas",
        "Ambiente laboral agradable",
        "Oportunidades de capacitación",
        "Descuentos especiales"
      ]
    },
    {
      id: 4,
      title: "Especialista en Marketing Digital",
      department: "Marketing",
      location: "Bogotá, Colombia / Remoto",
      type: "Tiempo Completo",
      experience: "2+ años",
      salary: "$3,000,000 - $4,500,000 COP",
      description: "Especialista en marketing digital para gestionar redes sociales, campañas y presencia online.",
      requirements: [
        "Profesional en Marketing, Comunicación o afines",
        "Experiencia en marketing digital y redes sociales",
        "Conocimiento en Google Ads, Facebook Ads",
        "Manejo de herramientas de análisis (Analytics)",
        "Creatividad y pensamiento estratégico"
      ],
      benefits: [
        "Trabajo híbrido/remoto",
        "Herramientas de trabajo",
        "Capacitación en tendencias",
        "Proyectos desafiantes",
        "Crecimiento profesional"
      ]
    }
  ];

  const benefits = [
    {
      icon: "💰",
      title: "Salario Competitivo",
      description: "Ofrecemos salarios acordes al mercado y tu experiencia"
    },
    {
      icon: "📚",
      title: "Capacitación Continua",
      description: "Cursos, seminarios y certificaciones para tu crecimiento"
    },
    {
      icon: "🏥",
      title: "Seguro Médico",
      description: "Cobertura médica completa para ti y tu familia"
    },
    {
      icon: "⏰",
      title: "Horarios Flexibles",
      description: "Balance vida-trabajo con horarios adaptables"
    },
    {
      icon: "🎯",
      title: "Crecimiento Profesional",
      description: "Oportunidades de ascenso y desarrollo de carrera"
    },
    {
      icon: "🎉",
      title: "Ambiente Positivo",
      description: "Equipo de trabajo colaborativo y profesional"
    }
  ];

  const [applicationForm, setApplicationForm] = useState({
    jobId: '',
    name: '',
    email: '',
    phone: '',
    resume: null,
    coverLetter: ''
  });

  const handleApplication = (jobId: number) => {
    setApplicationForm({ ...applicationForm, jobId: jobId.toString() });
    // Aquí iría la lógica para abrir el formulario de aplicación
    alert(`Aplicando para la posición ID: ${jobId}. Función de aplicación pendiente de implementar.`);
  };

  return (
      <>
        <Head title="Oportunidades Laborales - AESTHECTIC" />
        
        <div className="max-w-6xl mx-auto py-8">
          {/* Header */}
          <div className="text-center mb-12">
            <h1 className="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">
              Únete a Nuestro Equipo
            </h1>
            <p className="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
              Forma parte de AESTHECTIC y ayúdanos a transformar vidas a través de la medicina estética. 
              Ofrecemos un ambiente profesional donde podrás crecer y desarrollar tu carrera.
            </p>
          </div>

          {/* Company Culture */}
          <div className="mb-16">
            <h2 className="text-3xl font-bold text-center text-gray-900 dark:text-white mb-8">
              ¿Por qué trabajar con nosotros?
            </h2>
            <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
              {benefits.map((benefit, index) => (
                <div key={index} className="bg-white dark:bg-neutral-800 p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow">
                  <div className="text-4xl mb-4">{benefit.icon}</div>
                  <h3 className="text-xl font-bold text-gray-900 dark:text-white mb-3">
                    {benefit.title}
                  </h3>
                  <p className="text-gray-600 dark:text-gray-300">
                    {benefit.description}
                  </p>
                </div>
              ))}
            </div>
          </div>

          {/* Job Openings */}
          <div className="mb-16">
            <h2 className="text-3xl font-bold text-center text-gray-900 dark:text-white mb-8">
              Posiciones Disponibles
            </h2>
            
            <div className="space-y-6">
              {jobOpenings.map((job) => (
                <div key={job.id} className="bg-white dark:bg-neutral-800 rounded-xl shadow-lg overflow-hidden">
                  <div className="p-6">
                    <div className="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                      <div>
                        <h3 className="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                          {job.title}
                        </h3>
                        <div className="flex flex-wrap gap-2 text-sm">
                          <span className="bg-blue-100 text-blue-800 px-3 py-1 rounded-full">
                            {job.department}
                          </span>
                          <span className="bg-green-100 text-green-800 px-3 py-1 rounded-full">
                            {job.type}
                          </span>
                          <span className="bg-purple-100 text-purple-800 px-3 py-1 rounded-full">
                            {job.location}
                          </span>
                          <span className="bg-orange-100 text-orange-800 px-3 py-1 rounded-full">
                            {job.experience}
                          </span>
                        </div>
                      </div>
                      <div className="mt-4 md:mt-0 text-right">
                        <div className="text-lg font-bold text-green-600 mb-2">
                          {job.salary}
                        </div>
                        <button 
                          onClick={() => handleApplication(job.id)}
                          className="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors"
                        >
                          Aplicar
                        </button>
                      </div>
                    </div>

                    <p className="text-gray-600 dark:text-gray-300 mb-4">
                      {job.description}
                    </p>

                    <button
                      onClick={() => setSelectedJob(selectedJob === job.id ? null : job.id)}
                      className="text-blue-600 hover:text-blue-700 font-medium flex items-center"
                    >
                      {selectedJob === job.id ? 'Ver menos' : 'Ver detalles'}
                      <svg 
                        className={`w-4 h-4 ml-2 transition-transform ${selectedJob === job.id ? 'rotate-180' : ''}`}
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                      >
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                      </svg>
                    </button>

                    {selectedJob === job.id && (
                      <div className="mt-6 grid md:grid-cols-2 gap-6">
                        <div>
                          <h4 className="font-bold text-gray-900 dark:text-white mb-3">
                            Requisitos:
                          </h4>
                          <ul className="text-gray-600 dark:text-gray-300 space-y-2">
                            {job.requirements.map((req, index) => (
                              <li key={index} className="flex items-start">
                                <span className="text-green-500 mr-2">•</span>
                                {req}
                              </li>
                            ))}
                          </ul>
                        </div>
                        <div>
                          <h4 className="font-bold text-gray-900 dark:text-white mb-3">
                            Beneficios:
                          </h4>
                          <ul className="text-gray-600 dark:text-gray-300 space-y-2">
                            {job.benefits.map((benefit, index) => (
                              <li key={index} className="flex items-start">
                                <span className="text-blue-500 mr-2">•</span>
                                {benefit}
                              </li>
                            ))}
                          </ul>
                        </div>
                      </div>
                    )}
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* No current openings message */}
          {jobOpenings.length === 0 && (
            <div className="text-center py-12">
              <div className="text-6xl mb-4">🔍</div>
              <h3 className="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                No hay posiciones disponibles en este momento
              </h3>
              <p className="text-gray-600 dark:text-gray-300 mb-6">
                Pero siempre estamos interesados en conocer talento excepcional. 
                Envíanos tu hoja de vida para futuras oportunidades.
              </p>
            </div>
          )}

          {/* Spontaneous Application */}
          <div className="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl p-8 text-center text-white">
            <h3 className="text-2xl font-bold mb-4">
              ¿No encuentras la posición ideal?
            </h3>
            <p className="mb-6 text-blue-100">
              Envíanos tu perfil profesional y te contactaremos cuando surjan oportunidades 
              que se ajusten a tu experiencia.
            </p>
            <button className="bg-white text-blue-600 px-8 py-3 rounded-lg font-medium hover:bg-gray-100 transition-colors">
              Enviar CV Espontáneo
            </button>
          </div>

          {/* Contact Information */}
          <div className="mt-12 bg-gray-50 dark:bg-neutral-800 rounded-xl p-8">
            <h3 className="text-2xl font-bold text-gray-900 dark:text-white mb-6 text-center">
              Información de Contacto - RRHH
            </h3>
            
            <div className="grid md:grid-cols-3 gap-6 text-center">
              <div>
                <div className="text-3xl mb-3">📧</div>
                <h4 className="font-medium text-gray-900 dark:text-white mb-2">Email</h4>
                <p className="text-gray-600 dark:text-gray-300">rrhh@aesthectic.com</p>
              </div>
              
              <div>
                <div className="text-3xl mb-3">📞</div>
                <h4 className="font-medium text-gray-900 dark:text-white mb-2">Teléfono</h4>
                <p className="text-gray-600 dark:text-gray-300">+57 321 770 6324</p>
              </div>
              
              <div>
                <div className="text-3xl mb-3">📍</div>
                <h4 className="font-medium text-gray-900 dark:text-white mb-2">Ubicación</h4>
                <p className="text-gray-600 dark:text-gray-300">Bogotá, Colombia</p>
              </div>
            </div>

            <div className="mt-8 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
              <p className="text-sm text-blue-800 dark:text-blue-200 text-center">
                <strong>Nota:</strong> Solo contactaremos a los candidatos seleccionados para continuar 
                en el proceso. Agradecemos tu interés en formar parte de AESTHECTIC.
              </p>
            </div>
          </div>
        </div>
      </>
 
  );
}