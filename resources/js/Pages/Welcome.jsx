import {usePage} from "@inertiajs/react";
import ServicesList from "./Services/ServicesList.tsx";


export default function Welcome() {
    const props = usePage().props;

    return (
        <div className="container mx-auto">
            <div className="mb-10">
                <img src="https://i.pinimg.com/1200x/b6/19/2b/b6192b03134641e47fa92f7d8aba0e98.jpg"
                     className="w-full object-cover max-h-[300px] rounded-md" alt="Avatar"/>
            </div>
            <ServicesList services={props.services}/>
        </div>
    )
}
