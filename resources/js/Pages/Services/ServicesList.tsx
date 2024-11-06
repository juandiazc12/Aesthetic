import {Link} from '@inertiajs/react';

interface Props {
    services: Services
}

interface Services {
    current_page: number
    data: Daum[]
    first_page_url: string
    from: number
    last_page: number
    last_page_url: string
    links: Link[]
    next_page_url: any
    path: string
    per_page: number
    prev_page_url: any
    to: number
    total: number
}

export interface Daum {
    id: number
    name: string
    image: string
    slug: string
    description: string
    price: string
    created_at: string
    updated_at: string
}

export interface Link {
    url?: string
    label: string
    active: boolean
}

export default function ServicesList(props: Props) {
    const {services} = props
    return (
        <div className="grid grid-cols-1 gap-4 md:grid-cols-2  lg:grid-cols-3">
            {services.data.map((service, i) => (

                <div

                    key={i}
                    className="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-900 dark:border-neutral-700 dark:shadow-neutral-700/70">
                    <Link href={`/service/${service.slug}`}>
                        <img className="w-full h-auto rounded-t-xl"
                             src={service.image}
                             alt="Card Image"/>
                    </Link>

                    <div className="p-4 md:p-5">
                        <h3 className="text-lg font-bold text-gray-800 dark:text-white">
                            <Link className="underline" href={`/service/${service.slug}`}>
                                {service.name}
                            </Link>
                        </h3>
                        <p className="mt-1 text-gray-500 dark:text-neutral-400">
                            {service.description}
                        </p>
                        <p className="mt-2 font-extrabold dark:text-neutral-400">
                            {service.price}
                        </p>
                        <a className="mt-2 py-2 px-3 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                           href="#">
                            Booking now
                        </a>
                    </div>
                </div>
            ))}
        </div>
    )
}
