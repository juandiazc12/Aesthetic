import Layout from "@/Layouts/Layout";
import { Head, router } from "@inertiajs/react";
import { useState } from "react";

export default function PQRS() {
  const [formData, setFormData] = useState({
    type: "",
    name: "",
    email: "",
    phone: "",
    subject: "",
    message: "",
    attachments: [] as File[],
  });

  const [isSubmitting, setIsSubmitting] = useState(false);

  // Si usas Ziggy y tienes la ruta con nombre:
  // const pqrsUrl = route('pqrs.store');
  // Si no, deja la ruta literal:
  const pqrsUrl = "/pqrs";

  const handleInputChange = (
    e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>
  ) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const files = e.target.files ? Array.from(e.target.files) : [];
    setFormData((prev) => ({
      ...prev,
      attachments: files,
    }));
  };

  const resetForm = () => {
    setFormData({
      type: "",
      name: "",
      email: "",
      phone: "",
      subject: "",
      message: "",
      attachments: [],
    });
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);

    // Usamos FormData porque hay archivos
    const fd = new FormData();
    fd.append("type", formData.type);
    fd.append("name", formData.name);
    fd.append("email", formData.email);
    fd.append("phone", formData.phone);
    fd.append("subject", formData.subject);
    fd.append("message", formData.message);

    formData.attachments.forEach((file, i) => {
      // En backend puedes recibir como 'attachments[]'
      fd.append(`attachments[${i}]`, file);
    });

    router.post(pqrsUrl, fd, {
      forceFormData: true,
      onSuccess: () => {
        alert(
          "Su PQRS ha sido enviado exitosamente. Le responderemos en un plazo máximo de 15 días hábiles."
        );
        resetForm();
      },
      onError: (errors) => {
        console.error("Errores de validación:", errors);
        alert("Hubo un problema al enviar su PQRS. Verifique los campos.");
      },
      onFinish: () => {
        setIsSubmitting(false);
      },
    });
  };

  const pqrsTypes = [
    {
      value: "peticion",
      label: "Petición",
      description: "Solicitud respetuosa de información o documentos",
    },
    {
      value: "queja",
      label: "Queja",
      description: "Manifestación de inconformidad con un servicio",
    },
    {
      value: "reclamo",
      label: "Reclamo",
      description: "Solicitud de solución a un problema específico",
    },
    {
      value: "sugerencia",
      label: "Sugerencia",
      description: "Propuesta para mejorar nuestros servicios",
    },
  ];

  return (
    <>
      <Head title="PQRS - AESTHECTIC" />

      <div className="max-w-4xl mx-auto py-8">
        {/* Header */}
        <div className="text-center mb-12">
          <h1 className="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">
            PQRS
          </h1>
          <p className="text-lg text-gray-600 dark:text-gray-300">
            Peticiones, Quejas, Reclamos y Sugerencias
          </p>
          <p className="text-sm text-gray-500 dark:text-gray-400 mt-2">
            Tu opinión es importante para nosotros. Ayúdanos a mejorar nuestros
            servicios.
          </p>
        </div>

        {/* Information Cards */}
        <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-4 mb-12">
          {pqrsTypes.map((type, index) => (
            <div
              key={index}
              className="bg-white dark:bg-neutral-800 p-4 rounded-lg shadow-lg border-l-4 border-blue-600"
            >
              <h3 className="font-bold text-gray-900 dark:text-white mb-2">
                {type.label}
              </h3>
              <p className="text-sm text-gray-600 dark:text-gray-300">
                {type.description}
              </p>
            </div>
          ))}
        </div>

        {/* Form */}
        <div className="bg-white dark:bg-neutral-800 rounded-xl shadow-lg p-8">
          <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-6">
            Envía tu PQRS
          </h2>

          <form onSubmit={handleSubmit} className="space-y-6">
            {/* Tipo de PQRS */}
            <div>
              <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Tipo de PQRS *
              </label>
              <select
                name="type"
                value={formData.type}
                onChange={handleInputChange}
                required
                className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-neutral-700 dark:text-white"
              >
                <option value="">Selecciona una opción</option>
                {pqrsTypes.map((type) => (
                  <option key={type.value} value={type.value}>
                    {type.label}
                  </option>
                ))}
              </select>
            </div>

            {/* Información Personal */}
            <div className="grid md:grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Nombre Completo *
                </label>
                <input
                  type="text"
                  name="name"
                  value={formData.name}
                  onChange={handleInputChange}
                  required
                  className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-neutral-700 dark:text-white"
                />
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Correo Electrónico *
                </label>
                <input
                  type="email"
                  name="email"
                  value={formData.email}
                  onChange={handleInputChange}
                  required
                  className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-neutral-700 dark:text-white"
                />
              </div>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Teléfono (Opcional)
              </label>
              <input
                type="tel"
                name="phone"
                value={formData.phone}
                onChange={handleInputChange}
                className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-neutral-700 dark:text-white"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Asunto *
              </label>
              <input
                type="text"
                name="subject"
                value={formData.subject}
                onChange={handleInputChange}
                required
                className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-neutral-700 dark:text-white"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Mensaje *
              </label>
              <textarea
                name="message"
                value={formData.message}
                onChange={handleInputChange}
                required
                rows={6}
                className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-neutral-700 dark:text-white resize-none"
                placeholder="Describe detalladamente tu petición, queja, reclamo o sugerencia..."
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Adjuntos (Opcional)
              </label>
              <input
                type="file"
                name="attachments"
                multiple
                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                onChange={handleFileChange}
                className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-neutral-700 dark:text-white"
              />
              <p className="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Formatos permitidos: PDF, DOC, DOCX, JPG, JPEG, PNG. Máximo 5MB por archivo.
              </p>
              {formData.attachments.length > 0 && (
                <ul className="mt-2 text-xs text-gray-600 dark:text-gray-300 list-disc list-inside">
                  {formData.attachments.map((file, i) => (
                    <li key={i}>{file.name}</li>
                  ))}
                </ul>
              )}
            </div>

            <button
              type="submit"
              disabled={isSubmitting}
              className="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {isSubmitting ? "Enviando..." : "Enviar PQRS"}
            </button>
          </form>
        </div>

        {/* Contact Information */}
        <div className="mt-12 bg-gray-50 dark:bg-neutral-800 rounded-xl p-8">
          <h3 className="text-xl font-bold text-gray-900 dark:text-white mb-6">
            Otros Canales de Atención
          </h3>

          <div className="grid md:grid-cols-3 gap-6">
            <div className="text-center">
              <div className="text-3xl mb-3">📞</div>
              <h4 className="font-medium text-gray-900 dark:text-white mb-2">
                Teléfono
              </h4>
              <p className="text-gray-600 dark:text-gray-300">+57 321 770 6324</p>
              <p className="text-sm text-gray-500 dark:text-gray-400">
                Lun - Vie: 8:00 AM - 6:00 PM
              </p>
            </div>

            <div className="text-center">
              <div className="text-3xl mb-3">📧</div>
              <h4 className="font-medium text-gray-900 dark:text-white mb-2">
                Email
              </h4>
              <p className="text-gray-600 dark:text-gray-300">pqrs@aesthectic.com</p>
              <p className="text-sm text-gray-500 dark:text-gray-400">
                Respuesta en 24-48 horas
              </p>
            </div>

            <div className="text-center">
              <div className="text-3xl mb-3">💬</div>
              <h4 className="font-medium text-gray-900 dark:text-white mb-2">
                WhatsApp
              </h4>
              <p className="text-gray-600 dark:text-gray-300">+57 321 770 6324</p>
              <p className="text-sm text-gray-500 dark:text-gray-400">
                Lun - Vie: 8:00 AM - 6:00 PM
              </p>
            </div>
          </div>
        </div>

        {/* Legal Information */}
        <div className="mt-8 bg-blue-50 dark:bg-blue-900/20 rounded-xl p-6">
          <h4 className="font-bold text-blue-900 dark:text-blue-100 mb-3">
            Información Legal
          </h4>
          <div className="text-sm text-blue-800 dark:text-blue-200 space-y-2">
            <p>• Tiempo de respuesta: Máximo 15 días hábiles según la Ley 1755 de 2015.</p>
            <p>• Todos los datos proporcionados serán tratados según nuestra política de privacidad.</p>
            <p>• Las quejas y reclamos serán evaluados por nuestro equipo de calidad.</p>
            <p>• Te mantendremos informado sobre el estado de tu solicitud.</p>
          </div>
        </div>
      </div>
    </>
  );
}
