import { useNavigate } from "react-router-dom"
import { EditTemplateView } from "../pages/customer/campaign/component/editCampaignTemplate";

export const Actions =({
    campaign,
    setCampaignSection,
    setCampaignparams,
    campaignParams,
    deleteArray
})=>{
    const navigate = useNavigate();
    const handleClick=()=>{
        console.log(deleteArray)
    }
    return(
        <div className="row">
            <div className="col-md-6 mb-2">
            <div className="d-flex wrap align-items-center">
                    <div className="d-flex align-items-end mb-2 wrap">
                        <div  className="me-3">
                            <label 
                                htmlFor="sort"
                                className="me-1">
                                Sort By:
                            </label>
                            <select 
                                name="sort" 
                                id="sort"
                                className="btn rounded b-gainsboro me-2 mb-1"
                                >
                                {
                                    [
                                        {
                                            name:"Created At"
                                        },
                                        {
                                            name:"Name"
                                        }
                                    ]?.map((drop,index)=>{
                                        const {
                                            name
                                        }=drop
                                        return(
                                            <option 
                                                value={name}
                                                key={index}
                                            >{name}
                                            </option>
                                        )
                                    })
                                }
                            </select>
                        </div>
                        {
                            (deleteArray && deleteArray.length>0) &&( 
                                <button
                                    onClick={handleClick}
                                    className="btn btn-md  b-grey me-2 mb-1">
                                    delete
                                </button>
                            )
                        }
                        <input  
                            type="text"
                            placeholder="Type to search"
                            className="btn border rounded mb-1"
                        />
                    </div>
                </div>
            </div>
            <div className="col-md-6">
                {
                    campaign?(
                        <div>
                            <button 
                                type="button" 
                                className="btn b-grey btn-md my-2 fl-r mb-2"
                                onClick={()=>{
                                    setCampaignSection({
                                        name:"Template", 
                                        components:<EditTemplateView
                                            campaignParams={campaignParams}
                                            setCampaignparams={setCampaignparams}
                                            setCampaignSection={setCampaignSection}
                                        />
                                    })
                                }}
                            >
                                Create +
                            </button>
                        </div>
                    ):(
                        <div>
                            <button 
                                type="button" 
                                className="btn b-grey btn-md my-2 fl-r mb-2"
                                onClick={
                                    ()=>navigate("/create/template")
                                }
                            >
                                Create +
                            </button>
                        </div>
                    )
                }
            </div>
        </div>
    )
}