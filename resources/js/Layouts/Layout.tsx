import {Link, usePage} from "@inertiajs/react";
import React, {ReactElement} from "react";
import {Customer} from "../../Interfaces/Customer";

type PageProps = {
    customer: Customer
}


export default function Layout({children: children}: { children: ReactElement }) {

    const props = usePage<PageProps>().props;

    return (
        <>
            <div className="bg-white/60 backdrop-blur-lg dark:bg-neutral-900/60">
            <div className="max-w-[85rem] px-4 py-4 sm:px-6 lg:px-8 mx-auto">
                <div className="grid justify-center sm:grid-cols-2 sm:items-center gap-4">
                    <div className="flex items-center gap-x-3 md:gap-x-5">
                        <Link href="/">
                            <img src="../../img/logo/logo.png" alt="Logo" className="w-10 h-10 md:w-14 md:h-14" />
                        </Link>
                        <span className="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white">
                            AESTHECTIC
                        </span>
                    </div>


                        <div
                            className="text-center sm:text-start flex sm:justify-end sm:items-center gap-x-3 md:gap-x-4">

                            {!props.customer ? (
                                <>
                                    <Link
                                        className="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                                        href="/customer/login">
                                        Iniciar sesión
                                    </Link>
                                    <Link
                                        className="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-800 text-gray-800 hover:border-gray-500 hover:text-gray-500 focus:outline-none focus:text-gray-500 disabled:opacity-50 disabled:pointer-events-none dark:border-white dark:text-white dark:hover:text-neutral-300 dark:hover:border-neutral-300 dark:focus:text-neutral-300 dark:focus:border-neutral-300"
                                        href="/customer/register">
                                        Registrarse
                                    </Link>
                                </>
                            ) : (
                                <>
                                    <p className="capitalize">{props.customer.first_name}</p>
                                    <Link
                                        className="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-full border border-gray-800 text-gray-800 hover:border-gray-500 hover:text-gray-500 focus:outline-none focus:text-gray-500 disabled:opacity-50 disabled:pointer-events-none dark:border-white dark:text-white dark:hover:text-neutral-300 dark:hover:border-neutral-300 dark:focus:text-neutral-300 dark:focus:border-neutral-300"
                                        href="/customer/logout">
                                        Cerrar sesión
                                    </Link>
                                </>
                            )}


                        </div>

                    </div>

                </div>
            </div>

            <main>{children}</main>
        </>
    );
}
