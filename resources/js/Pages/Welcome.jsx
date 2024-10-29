import {usePage} from "@inertiajs/react";


export default function Welcome() {

    const props = usePage().props;

    console.log(props)

    return (
        <div className="container mx-auto">Hola con laravel y react</div>
    )
}
