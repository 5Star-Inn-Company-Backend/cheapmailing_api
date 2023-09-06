import { useEffect } from "react";
import { useDispatch, useSelector } from "react-redux";
import Spinner from "../../../../../components/spinner/spinner";
import { GetInviteSent, collab_SliceActions } from "../../../../../store/collaborationSlice";
import { Actions } from "./collabAction";
import { NoData } from "../../../../../components/nodata";
import { ViewportList } from "react-viewport-list";
import { useRef } from "react";

export const InvitesContainer =()=>{
    const collab = useSelector(
        state => state.collab
    )
    const dispatch = useDispatch();
    useEffect(()=>{
        dispatch(GetInviteSent(null));
    },[dispatch])
    const listRef = useRef(null)
    const handleInputChange=(e)=>{
        dispatch(collab_SliceActions.searchdata({
            type:"invite",
            data:e.target.value
        }))
    }

    if(collab.GetInviteSentStatus ==='pending'){
        return <Spinner/>
    }

    return(
        <>
        <Actions
            handleInputChange={handleInputChange}
            actionName="Invite Collaborators"
        />
        <div className="w-overflow" ref={listRef}>
            <table className=" table table-striped table-hover table-bordered table-responsive caption-top mb-3">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Invites Sent</th>
                        <th scope="col">Email</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                <ViewportList viewportRef={listRef} items={collab.inviteSent} margin={8}>
                    {
                        (
                            (collaboration,index)=>{
                            const{
                                name,
                                email
                            }= collaboration
                        
                            return(
                                <tr key={index}>
                                    <th scope="row" className="fs-6">{index +1}</th>
                                    <td className="fs-6">{name}</td>
                                    <td className="fs-6">{email}</td>
                                    <td>
                                        <div className="d-flex align-items-center">
                                            <div className="dropdown">
                                                <button
                                                    className="btn btn-secondary dropdown-toggle" 
                                                    type="button" 
                                                    data-bs-toggle="dropdown" 
                                                    aria-expanded="false"  
                                                >
                                                </button>
                                                <ul className="dropdown-menu">
                                                    <li
                                                        className="dropdown-item fs-6"
                                                    >
                                                        Delete
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            )
                        })
                    }
                    </ViewportList>
                </tbody>
            </table>
        </div>
        
        {
            collab
            .inviteSent?.length === 0 && <NoData/>
        }
        </>
    )
}